<?php

namespace App\Http\Controllers;

use App\Models\BadgeColumn;
use App\Models\BadgesArea;
use App\Models\BadgesZone;
use App\Models\EventBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class EventBadgeController extends Controller
{
    public function saveEventBadge(Request $request, string $event_id, string $badge_id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        $request->validate([
            'fileName' => 'required|string|max:300',
            'startTemplateUrl' => 'sometimes|string|nullable',
            'html' => 'required',
        ]);
        $urlparts = pathinfo(parse_url($request->fileName)['path']);
        $check = EventBadge::where('badge_id', $badge_id)->where('event_id', $event_id)->first();

        $doc = new \DOMDocument();
        $doc->loadHTML($request->html);
        $columns = [];
        $ids = [];
        $areaIds = [];

        foreach ($doc->getElementsByTagName('*') as $element) {
            if (!empty($element->getAttribute('key'))) {
                $columns[] = $element->getAttribute('key');
                if (!empty($element->getAttribute('id'))  && $element->getAttribute('key') == 'zone') {
                    $ids[] = $element->getAttribute('id');
                    $areaIds[] = '';
                } else if (!empty($element->getAttribute('id'))  && $element->getAttribute('key') == 'area') {
                    $areaIds[] = $element->getAttribute('id');
                    $ids[] = '';
                } else {
                    $ids[] = '';
                    $areaIds[] = '';
                }
            }
        }

        if (!$check) {
            $create = new EventBadge();
            $create->fileName = $urlparts['dirname'] . '/' . $urlparts['filename'] . '_' . $event_id . '_' . $badge_id . '.' . $urlparts['extension'];
            $create->startTemplateUrl = $request->startTemplateUrl;
            $create->html = $request->html;
            $create->event_id = $event_id;
            $create->badge_id = $badge_id;
            $create->status = 0;
            if ($create->save()) {
                $extracted = $urlparts['dirname'] . '/' . $urlparts['filename'] . '_' . $event_id . '_' . $badge_id . '.' . $urlparts['extension'];

                File::put(public_path() . '/' . $extracted, $request->html);

                foreach ($columns as $col) {
                    BadgeColumn::create([
                        'event_id' => $event_id,
                        'badge_id' => $badge_id,
                        'title' => $col
                    ]);
                }

                DB::commit();
                return response()->json(['success' => true, 'details' => 'Created successfully']);
            } else {

                return response()->json(['danger' => false, 'details' => 'Failed to create the form.']);
            }
        } else {
            $getFile = explode('_', $urlparts['filename']);

            $updateCheck = $check->update([
                'fileName' => $urlparts['dirname'] . '/' . $getFile[0] . '_' . $event_id . '_' . $badge_id . '.' . $urlparts['extension'],
                'startTemplateUrl' => $request->startTemplateUrl,
                'html' => $request->html,
                'event_id' => $event_id,
                'badge_id' => $badge_id,
                'status' => 0
            ]);
            // dd($request->html);
            if ($updateCheck) {

                $extracted = $urlparts['dirname'] . '/' . $getFile[0] . '_' . $event_id . '_' . $badge_id . '.' . $urlparts['extension'];

                // dd(File::exists(public_path().'/'.$extracted));
                if (File::exists(public_path() . '/' . $extracted)) {
                    File::delete(public_path() . '/' . $extracted);
                }
                File::put(public_path() . '/' . $extracted, $request->html);

                if (BadgeColumn::where('event_id', $event_id)->where('badge_id', $badge_id)->count() > 0) BadgeColumn::where('event_id', $event_id)->where('badge_id', $badge_id)->delete();
                if (BadgesZone::where('event_id', $event_id)->where('badge_id', $badge_id)->count() > 0) BadgesZone::where('event_id', $event_id)->where('badge_id', $badge_id)->delete();
                if (BadgesArea::where('event_id', $event_id)->where('badge_id', $badge_id)->count() > 0) BadgesArea::where('event_id', $event_id)->where('badge_id', $badge_id)->delete();

                foreach ($columns as $k => $col) {
                    if ($col == 'zone') {
                        BadgesZone::create([
                            'event_id' => $event_id,
                            'badge_id' => $badge_id,
                            'zone_id' => $ids[$k]
                        ]);
                    }
                    if ($col == 'area') {
                        BadgesArea::create([
                            'event_id' => $event_id,
                            'badge_id' => $badge_id,
                            'area_id' => $areaIds[$k]
                        ]);
                    }
                    BadgeColumn::create([
                        'event_id' => $event_id,
                        'badge_id' => $badge_id,
                        'title' => $col
                    ]);
                }

                DB::commit();
                return response()->json(['success' => true, 'details' => 'Updated successfully']);
            } else {
                return response()->json(['danger' => false, 'details' => 'Failed to update']);
            }
        }
    }
}
