<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ URL::asset('favicon.ico') }}">
    <base href="">
    <title>E-ticket</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link href="{{ URL::asset('EasyticketJs-master/css/editor.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('EasyticketJs-master/css/line-awesome.css') }}" rel="stylesheet">
</head>
<body>
<div id="easyticket-builder">
    <div id="top-panel">
        <img style="width: 75px;" class="logo responsive-image float-left" id="logo" alt="E-ticket"
             src="/logo-dark.jpeg"/>
        <div style="padding-left:40px; margin-right:20px; margin-top:10px;" class="btn " title=" left column">
            <a class="btn btn-info pull-left" href="/event/{{$event->id}}/badges">Go Back</a>
        </div>
        <div class="btn-group float-right" role="group" style="margin-top:10px;">
            <button class="btn btn-light" title="Toggle file manager" id="toggle-file-manager-btn"
                    data-easyticket-action="toggleFileManager" data-toggle="button" aria-pressed="false">
                <img src="{{ URL::asset('EasyticketJs-master/libs/builder/icons/file-manager-layout.svg') }}"
                     width="20px" height="20px">
            </button>
            <button class="btn btn-light" title="Toggle left column" id="toggle-left-column-btn"
                    data-easyticket-action="toggleLeftColumn" data-toggle="button" aria-pressed="false">
                <img src="{{ URL::asset('EasyticketJs-master/libs/builder/icons/left-column-layout.svg') }}"
                     width="20px" height="20px">
            </button>
            <button class="btn btn-light" title="Toggle right column" id="toggle-right-column-btn"
                    data-easyticket-action="toggleRightColumn" data-toggle="button" aria-pressed="false">
                <img src="{{ URL::asset('EasyticketJs-master/libs/builder/icons/right-column-layout.svg') }}"
                     width="20px" height="20px">
            </button>
        </div>
        <div class="btn-group mr-3" role="group" style="margin-top:10px;">
            <button class="btn btn-light" title="Undo (Ctrl/Cmd + Z)" id="undo-btn" data-easyticket-action="undo"
                    data-easyticket-shortcut="ctrl+z">
                <i class="la la-undo"></i>
            </button>
            <button class="btn btn-light" title="Redo (Ctrl/Cmd + Shift + Z)" id="redo-btn"
                    data-easyticket-action="redo" data-easyticket-shortcut="ctrl+shift+z">
                <i class="la la-undo la-flip-horizontal"></i>
            </button>
        </div>
        <div class="btn-group mr-3" role="group" style="margin-top:10px;">
            <button class="btn btn-light" title="Designer Mode (Free component dragging)" id="designer-mode-btn"
                    data-toggle="button" aria-pressed="false" data-easyticket-action="setDesignerMode">
                <i class="la la-hand-grab-o"></i>
            </button>
            <button class="btn btn-light" title="Preview" id="preview-btn" type="button" data-toggle="button"
                    aria-pressed="false" data-easyticket-action="preview">
                <i class="la la-eye"></i>
            </button>
            <button class="btn btn-light" title="Fullscreen (F11)" id="fullscreen-btn" data-toggle="button"
                    aria-pressed="false" data-easyticket-action="fullscreen">
                <i class="la la-arrows"></i>
            </button>
        </div>
        <div class="btn-group mr-3" role="group" style="margin-top:10px;">
            <button class="btn btn-light" title="Export (Ctrl + E)" id="save-btn" data-easyticket-action="saveAjax"
                    data-easyticket-shortcut="ctrl+e">
                <input name="event_id" id="event_id" type="hidden" value="{{ $event->id }}"/>
                <input name="badge_id" id="badge_id" type="hidden" value="{{ $badge->id }}"/>
                <input name="store_url" id="store_url" type="hidden" value="{{ $store_url }}"/>
                <i class="la la-save"></i>
            </button>
            <button class="btn btn-light" title="Download" id="download-btn" data-easyticket-action="download"
                    data-download="index.html">
                <i class="la la-download"></i>
            </button>
        </div>
        <div class="btn-group float-right responsive-btns" role="group" style="margin-top:10px;">
            <button id="mobile-view" data-view="mobile" class="btn btn-light" title="Mobile view"
                    data-easyticket-action="viewport">
                <i class="la la-mobile-phone"></i>
            </button>
            <button id="tablet-view" data-view="tablet" class="btn btn-light" title="Tablet view"
                    data-easyticket-action="viewport">
                <i class="la la-tablet"></i>
            </button>
            <button id="desktop-view" data-view="" class="btn btn-light" title="Desktop view"
                    data-easyticket-action="viewport">
                <i class="la la-laptop"></i>
            </button>
        </div>
    </div>
    <div id="left-panel" style="margin-top: 30px;">
        <div id="filemanager">
            <div class="header">
                <a href="#" class="text-secondary">This should be list of samples xxxx</a>

            </div>
            <div class="tree">
                {{--  <ol>
                </ol>  --}}
            </div>
        </div>
        <div class="drag-elements">
            <div class="header">
                <ul class="nav nav-tabs" id="elements-tabs" role="tablist" style="display: none">
                    <li class="nav-item component-tab">
                        <a class="nav-link active" id="components-tab" data-toggle="tab" href="#components" role="tab"
                           aria-controls="components" aria-selected="true">
                            <i class="la la-lg la-cube"></i>
                            <div><small>Components</small></div>
                        </a>
                    </li>
                    <li class="nav-item blocks-tab">
                        <a class="nav-link" id="blocks-tab" data-toggle="tab" href="#blocks" role="tab"
                           aria-controls="blocks" aria-selected="false">
                            <i class="la la-lg la-image"></i>
                            <div><small>Blocks</small></div>
                        </a>
                    </li>
                    <li class="nav-item component-properties-tab" style="display:none">
                        <a class="nav-link" id="properties-tab" data-toggle="tab" href="#properties" role="tab"
                           aria-controls="blocks" aria-selected="false">
                            <i class="la la-lg la-cog"></i>
                            <div><small>Properties</small></div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="components" role="tabpanel"
                         aria-labelledby="components-tab">
                        <div class="search">
                            <input class="form-control form-control-sm component-search" placeholder="Search components"
                                   type="text" data-easyticket-action="componentSearch" data-easyticket-on="keyup">
                            <button class="clear-backspace" data-easyticket-action="clearComponentSearch">
                                <i class="la la-close"></i>
                            </button>
                        </div>
                        <div class="drag-elements-sidepane sidepane">
                            <div>
                                <ul class="components-list clearfix" data-type="leftpanel">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="blocks" role="tabpanel" aria-labelledby="blocks-tab">
                        <div class="search">
                            <input class="form-control form-control-sm block-search" placeholder="Search blocks"
                                   type="text" data-easyticket-action="blockSearch" data-easyticket-on="keyup">
                            <button class="clear-backspace" data-easyticket-action="clearBlockSearch">
                                <i class="la la-close"></i>
                            </button>
                        </div>
                        <div class="drag-elements-sidepane sidepane">
                            <div>
                                <ul class="blocks-list clearfix" data-type="leftpanel">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="properties" role="tabpanel" aria-labelledby="blocks-tab">
                        <div class="component-properties-sidepane">
                            <div>
                                <div class="component-properties">
                                    <ul class="nav nav-tabs nav-fill" id="properties-tabs" role="tablist">
                                        <li class="nav-item content-tab">
                                            <a class="nav-link active" data-toggle="tab" href="#content-left-panel-tab"
                                               role="tab" aria-controls="components" aria-selected="true">
                                                <i class="la la-lg la-cube"></i>
                                                <div><span>Content</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item style-tab">
                                            <a class="nav-link" data-toggle="tab" href="#style-left-panel-tab"
                                               role="tab" aria-controls="blocks" aria-selected="false">
                                                <i class="la la-lg la-image"></i>
                                                <div><span>Style</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item advanced-tab">
                                            <a class="nav-link" data-toggle="tab" href="#advanced-left-panel-tab"
                                               role="tab" aria-controls="blocks" aria-selected="false">
                                                <i class="la la-lg la-cog"></i>
                                                <div><span>Advanced</span></div>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="content-left-panel-tab"
                                             data-section="content" role="tabpanel" aria-labelledby="content-tab">
                                            <div class="mt-4 text-center">Click on an element to edit.</div>
                                        </div>
                                        <div class="tab-pane fade show" id="style-left-panel-tab" data-section="style"
                                             role="tabpanel" aria-labelledby="style-tab">
                                        </div>
                                        <div class="tab-pane fade show" id="advanced-left-panel-tab"
                                             data-section="advanced" role="tabpanel" aria-labelledby="advanced-tab">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="canvas" style="margin-top: 30px;">
        <div id="iframe-wrapper">
            <div id="iframe-layer">
                <div id="highlight-box">
                    <div id="highlight-name"></div>
                    <div id="section-actions">
                        <a id="add-section-btn" href="" title="Add element"><i class="la la-plus"></i></a>
                    </div>
                </div>
                <div id="select-box">
                    <div id="wysiwyg-editor">
                        <a id="bold-btn" href="" title="Bold"><i><strong>B</strong></i></a>
                        <a id="italic-btn" href="" title="Italic"><i>I</i></a>
                        <a id="underline-btn" href="" title="Underline"><u>u</u></a>
                        <a id="strike-btn" href="" title="Strikeout">
                            <del>S</del>
                        </a>
                        <a id="link-btn" href="" title="Create link"><strong>a</strong></a>
                    </div>
                    <div id="select-actions">
                        <a id="drag-btn" href="" title="Drag element"><i class="la la-arrows"></i></a>
                        <a id="parent-btn" href="" title="Select parent"><i class="la la-level-down la-rotate-180"></i></a>
                        <a id="up-btn" href="" title="Move element up"><i class="la la-arrow-up"></i></a>
                        <a id="down-btn" href="" title="Move element down"><i class="la la-arrow-down"></i></a>
                        <a id="clone-btn" href="" title="Clone element"><i class="la la-copy"></i></a>
                        <a id="delete-btn" href="" title="Remove element"><i class="la la-trash"></i></a>
                    </div>
                </div>
                <!-- add section box -->
                <div id="add-section-box" class="drag-elements">
                    <div class="header">
                        <ul class="nav nav-tabs" id="box-elements-tabs" role="tablist">
                            <li class="nav-item component-tab">
                                <a class="nav-link active" id="box-components-tab" data-toggle="tab"
                                   href="#box-components" role="tab" aria-controls="components" aria-selected="true">
                                    <i class="la la-lg la-cube"></i>
                                    <div><small>Components</small></div>
                                </a>
                            </li>
                            <li class="nav-item blocks-tab">
                                <a class="nav-link" id="box-blocks-tab" data-toggle="tab" href="#box-blocks" role="tab"
                                   aria-controls="blocks" aria-selected="false">
                                    <i class="la la-lg la-image"></i>
                                    <div><small>Blocks</small></div>
                                </a>
                            </li>
                            <li class="nav-item component-properties-tab" style="display:none">
                                <a class="nav-link" id="box-properties-tab" data-toggle="tab" href="#box-properties"
                                   role="tab" aria-controls="blocks" aria-selected="false">
                                    <i class="la la-lg la-cog"></i>
                                    <div><small>Properties</small></div>
                                </a>
                            </li>
                        </ul>
                        <div class="section-box-actions">
                            <div id="close-section-btn" class="btn btn-light btn-sm bg-white btn-sm float-right"><i
                                    class="la la-close"></i></div>
                            <div class="small mt-1 mr-3 float-right">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="add-section-insert-mode-after" value="after"
                                           checked="checked" name="add-section-insert-mode"
                                           class="custom-control-input">
                                    <label class="custom-control-label"
                                           for="add-section-insert-mode-after">After</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="add-section-insert-mode-inside" value="inside"
                                           name="add-section-insert-mode" class="custom-control-input">
                                    <label class="custom-control-label"
                                           for="add-section-insert-mode-inside">Inside</label>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="box-components" role="tabpanel"
                                 aria-labelledby="components-tab">
                                <div class="search">
                                    <input class="form-control form-control-sm component-search"
                                           placeholder="Search components" type="text"
                                           data-easyticket-action="addBoxComponentSearch" data-easyticket-on="keyup">
                                    <button class="clear-backspace" data-easyticket-action="clearComponentSearch">
                                        <i class="la la-close"></i>
                                    </button>
                                </div>
                                <div>
                                    <div>
                                        <ul class="components-list clearfix" data-type="addbox">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="box-blocks" role="tabpanel" aria-labelledby="blocks-tab">
                                <div class="search">
                                    <input class="form-control form-control-sm block-search" placeholder="Search blocks"
                                           type="text" data-easyticket-action="addBoxBlockSearch"
                                           data-easyticket-on="keyup">
                                    <button class="clear-backspace" data-easyticket-action="clearBlockSearch">
                                        <i class="la la-close"></i>
                                    </button>
                                </div>
                                <div>
                                    <div>
                                        <ul class="blocks-list clearfix" data-type="addbox">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- div class="tab-pane fade" id="box-properties" role="tabpanel" aria-labelledby="blocks-tab">
                               <div class="component-properties-sidepane">
                                   <div>
                                       <div class="component-properties">
                                           <div class="mt-4 text-center">Click on an element to edit.</div>
                                       </div>
                                   </div>
                               </div>
                               </div -->
                        </div>
                    </div>
                </div>
                <!-- //add section box -->
            </div>
            <iframe src="about:none" id="iframe1"></iframe>
        </div>
    </div>
    <div id="right-panel" style="margin-top: 30px;">
        <div class="component-properties">
            <ul class="nav nav-tabs nav-fill" id="properties-tabs" role="tablist">
                <li class="nav-item content-tab">
                    <a class="nav-link active" data-toggle="tab" href="#content-tab" role="tab"
                       aria-controls="components" aria-selected="true">
                        <i class="la la-lg la-cube"></i>
                        <div><span>Content</span></div>
                    </a>
                </li>
                <li class="nav-item style-tab">
                    <a class="nav-link" data-toggle="tab" href="#style-tab" role="tab" aria-controls="blocks"
                       aria-selected="false">
                        <i class="la la-lg la-image"></i>
                        <div><span>Style</span></div>
                    </a>
                </li>
                <li class="nav-item advanced-tab">
                    <a class="nav-link" data-toggle="tab" href="#advanced-tab" role="tab" aria-controls="blocks"
                       aria-selected="false">
                        <i class="la la-lg la-cog"></i>
                        <div><span>Advanced</span></div>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="content-tab" data-section="content" role="tabpanel"
                     aria-labelledby="content-tab">
                </div>
                <div class="tab-pane fade show" id="style-tab" data-section="style" role="tabpanel"
                     aria-labelledby="style-tab">
                </div>
                <div class="tab-pane fade show" id="advanced-tab" data-section="advanced" role="tabpanel"
                     aria-labelledby="advanced-tab">
                </div>
            </div>
        </div>
    </div>
    <div id="bottom-panel">
        <div class="btn-group" role="group">
            <button id="code-editor-btn" data-view="mobile" class="btn btn-sm btn-light btn-sm" title="Code editor"
                    data-easyticket-action="toggleEditor">
                <i class="la la-code"></i> Code editor
            </button>
            <div id="toggleEditorJsExecute" class="custom-control custom-checkbox mt-1" style="display:none">
                <input type="checkbox" class="custom-control-input" id="customCheck" name="example1"
                       data-easyticket-action="toggleEditorJsExecute">
                <label class="custom-control-label" for="customCheck"><small>Run javascript code on edit</small></label>
            </div>
        </div>
        <div id="easyticket-code-editor">
            <textarea class="form-control"></textarea>
            <div>
            </div>
        </div>
    </div>
    <!-- templates -->
    <script id="easyticket-input-textinput" type="text/html">
        <div>
            <input name="{%=key%}" type="text" class="form-control"/>
        </div>

    </script>
    <script id="easyticket-input-textareainput" type="text/html">
        <div>
            <textarea name="{%=key%}" rows="3" class="form-control"></textarea>
        </div>

    </script>
    <script id="easyticket-input-checkboxinput" type="text/html">
        <div class="custom-control custom-checkbox">
            <input name="{%=key%}" class="custom-control-input" type="checkbox" id="{%=key%}_check">
            <label class="custom-control-label" for="{%=key%}_check">{% if (typeof text !== 'undefined') { %} {%=text%}
                {% } %}</label>
        </div>

    </script>
    <script id="easyticket-input-radioinput" type="text/html">
        <div>

            {% for ( var i = 0; i < options.length; i++ ) { %}

            <label
                class="custom-control custom-radio  {% if (typeof inline !== 'undefined' && inline == true) { %}custom-control-inline{% } %}"
                title="{%=options[i].title%}">
                <input name="{%=key%}" class="custom-control-input" type="radio" value="{%=options[i].value%}"
                       id="{%=key%}{%=i%}" {%if (options[i].checked) { %}checked="{%=options[i].checked%}" {% } %}>
                <label class="custom-control-label" for="{%=key%}{%=i%}">{%=options[i].text%}</label>
            </label>

            {% } %}

        </div>

    </script>
    <script id="easyticket-input-radiobuttoninput" type="text/html">
        <div class="btn-group btn-group-toggle  {%if (extraclass) { %}{%=extraclass%}{% } %} clearfix"
             data-toggle="buttons">

            {% for ( var i = 0; i < options.length; i++ ) { %}

            <label
                class="btn  btn-outline-primary  {%if (options[i].checked) { %}active{% } %}  {%if (options[i].extraclass) { %}{%=options[i].extraclass%}{% } %}"
                for="{%=key%}{%=i%} " title="{%=options[i].title%}">
                <input name="{%=key%}" class="custom-control-input" type="radio" value="{%=options[i].value%}"
                       id="{%=key%}{%=i%}" {%if (options[i].checked) { %}checked="{%=options[i].checked%}" {% } %}>
                {%if (options[i].icon) { %}<i class="{%=options[i].icon%}"></i>{% } %}
                {%=options[i].text%}
            </label>

            {% } %}

        </div>

    </script>
    <script id="easyticket-input-toggle" type="text/html">
        <div class="toggle">
            <input type="checkbox" name="{%=key%}" value="{%=on%}" data-value-off="{%=off%}" data-value-on="{%=on%}"
                   class="toggle-checkbox" id="{%=key%}">
            <label class="toggle-label" for="{%=key%}">
                <span class="toggle-inner"></span>
                <span class="toggle-switch"></span>
            </label>
        </div>

    </script>
    <script id="easyticket-input-header" type="text/html">
        <h6 class="header">{%=header%}</h6>

    </script>
    <script id="easyticket-input-select" type="text/html">
        <div>

            <select class="form-control custom-select">
                {% for ( var i = 0; i < options.length; i++ ) { %}
                <option value="{%=options[i].value%}">{%=options[i].text%}</option>
                {% } %}
            </select>

        </div>

    </script>
    <script id="easyticket-input-listinput" type="text/html">
        <div class="row">

            {% for ( var i = 0; i < options.length; i++ ) { %}
            <div class="col-6">
                <div class="input-group">
                    <input name="{%=key%}_{%=i%}" type="text" class="form-control" value="{%=options[i].text%}"/>
                    <div class="input-group-append">
                        <button class="input-group-text btn btn-sm btn-danger">
                            <i class="la la-trash la-lg"></i>
                        </button>
                    </div>
                </div>
                <br/>
            </div>
            {% } %}


            {% if (typeof hide_remove === 'undefined') { %}
            <div class="col-12">

                <button class="btn btn-sm btn-outline-primary">
                    <i class="la la-trash la-lg"></i> Add new
                </button>

            </div>
            {% } %}

        </div>

    </script>
    <script id="easyticket-input-grid" type="text/html">
        <div class="row">
            <div class="mb-1 col-12">

                <label>Flexbox</label>
                <select class="form-control custom-select" name="col">

                    <option value="">None</option>
                    {% for ( var i = 1; i <= 12; i++ ) { %}
                    <option value="{%=i%}" {% if ((typeof col !==
                    'undefined') && col == i) { %} selected {% } %}>{%=i%}</option>
                    {% } %}

                </select>
                <br/>
            </div>

            <div class="col-6">
                <label>Extra small</label>
                <select class="form-control custom-select" name="col-xs">

                    <option value="">None</option>
                    {% for ( var i = 1; i <= 12; i++ ) { %}
                    <option value="{%=i%}" {% if ((typeof col_xs !==
                    'undefined') && col_xs == i) { %} selected {% } %}>{%=i%}</option>
                    {% } %}

                </select>
                <br/>
            </div>

            <div class="col-6">
                <label>Small</label>
                <select class="form-control custom-select" name="col-sm">

                    <option value="">None</option>
                    {% for ( var i = 1; i <= 12; i++ ) { %}
                    <option value="{%=i%}" {% if ((typeof col_sm !==
                    'undefined') && col_sm == i) { %} selected {% } %}>{%=i%}</option>
                    {% } %}

                </select>
                <br/>
            </div>

            <div class="col-6">
                <label>Medium</label>
                <select class="form-control custom-select" name="col-md">

                    <option value="">None</option>
                    {% for ( var i = 1; i <= 12; i++ ) { %}
                    <option value="{%=i%}" {% if ((typeof col_md !==
                    'undefined') && col_md == i) { %} selected {% } %}>{%=i%}</option>
                    {% } %}

                </select>
                <br/>
            </div>

            <div class="col-6 mb-1">
                <label>Large</label>
                <select class="form-control custom-select" name="col-lg">

                    <option value="">None</option>
                    {% for ( var i = 1; i <= 12; i++ ) { %}
                    <option value="{%=i%}" {% if ((typeof col_lg !==
                    'undefined') && col_lg == i) { %} selected {% } %}>{%=i%}</option>
                    {% } %}

                </select>
                <br/>
            </div>

            {% if (typeof hide_remove === 'undefined') { %}
            <div class="col-12">

                <button class="btn btn-sm btn-outline-light text-danger">
                    <i class="la la-trash la-lg"></i> Remove
                </button>

            </div>
            {% } %}

        </div>

    </script>
    <script id="easyticket-input-textvalue" type="text/html">
        <div class="row">
            <div class="col-6 mb-1">
                <label>Value</label>
                <input name="value" type="text" value="{%=value%}" class="form-control"/>
            </div>

            <div class="col-6 mb-1">
                <label>Text</label>
                <input name="text" type="text" value="{%=text%}" class="form-control"/>
            </div>

            {% if (typeof hide_remove === 'undefined') { %}
            <div class="col-12">

                <button class="btn btn-sm btn-outline-light text-danger">
                    <i class="la la-trash la-lg"></i> Remove
                </button>

            </div>
            {% } %}

        </div>

    </script>
    <script id="easyticket-input-rangeinput" type="text/html">
        <div>
            <input name="{%=key%}" type="range" min="{%=min%}" max="{%=max%}" step="{%=step%}" class="form-control"/>
        </div>

    </script>
    <script id="easyticket-input-imageinput" type="text/html">
        <div>
            <input name="{%=key%}" type="text" class="form-control"/>
            <input name="file" type="file" class="form-control"/>
        </div>

    </script>
    <script id="easyticket-input-colorinput" type="text/html">
        <div>
            <input name="{%=key%}" type="color" {% if (typeof value !== 'undefined' && value != false) { %}
            value="{%=value%}" {% } %} pattern="#[a-f0-9]{6}" class="form-control"/>
        </div>

    </script>
    <script id="easyticket-input-bootstrap-color-picker-input" type="text/html">
        <div>
            <div id="cp2" class="input-group" title="Using input value">
                <input name="{%=key%}" type="text" {% if (typeof value !== 'undefined' && value != false) { %}
                value="{%=value%}" {% } %} class="form-control"/>
                <span class="input-group-append">
            		<span class="input-group-text colorpicker-input-addon"><i></i></span>
            	  </span>
            </div>
        </div>

    </script>
    <script id="easyticket-input-numberinput" type="text/html">
        <div>
            <input name="{%=key%}" type="number" value="{%=value%}"
                   {% if (typeof min !== 'undefined' && min != false) { %}min="{%=min%}"{% } %}
            {% if (typeof max !== 'undefined' && max != false) { %}max="{%=max%}"{% } %}
            {% if (typeof step !== 'undefined' && step != false) { %}step="{%=step%}"{% } %}
            class="form-control"/>
        </div>
    </script>
    <script id="easyticket-input-button" type="text/html">
        <div>
            <button class="btn btn-sm btn-primary">
                <i class="la  {% if (typeof icon !== 'undefined') { %} {%=icon%} {% } else { %} la-plus {% } %} la-lg"></i>
                {%=text%}
            </button>
        </div>
    </script>
    <script id="easyticket-input-cssunitinput" type="text/html">
        <div class="input-group" id="cssunit-{%=key%}">
            <input name="number" type="number" {% if (typeof value !== 'undefined' && value != false) { %}
            value="{%=value%}" {% } %}
            {% if (typeof min !== 'undefined' && min != false) { %}min="{%=min%}"{% } %}
            {% if (typeof max !== 'undefined' && max != false) { %}max="{%=max%}"{% } %}
            {% if (typeof step !== 'undefined' && step != false) { %}step="{%=step%}"{% } %}
            class="form-control"/>
            <div class="input-group-append">
                <select class="form-control custom-select small-arrow" name="unit">
                    <option selected value="px">px</option>
                    <option value="em">em</option>
                    <option value="%">%</option>
                    <option value="rem">rem</option>
                    <option value="auto">auto</option>
                </select>
            </div>
        </div>

    </script>
    <script id="easyticket-filemanager-folder" type="text/html">
        <li data-folder="{%=folder%}" class="folder">
            <label for="{%=folder%}"><span>{%=folderTitle%}</span></label> <input type="checkbox" id="{%=folder%}"/>
            <ol></ol>
        </li>
    </script>
    <script id="easyticket-filemanager-page" type="text/html">
        <li data-url="{%=url%}" data-page="{%=name%}" class="file">
            <label for="{%=name%}"><span>{%=title%}</span></label> <input type="checkbox" checked id="{%=name%}"/>
            <ol></ol>
        </li>
    </script>
    <script id="easyticket-filemanager-component" type="text/html">
        <li data-url="{%=url%}" data-component="{%=name%}" class="component">
            <a href="{%=url%}"><span>{%=title%}</span></a>
        </li>
    </script>
    <script id="easyticket-input-sectioninput" type="text/html">
        <label class="header" data-header="{%=key%}" for="header_{%=key%}"><span>&ensp;{%=header%}</span>
            <div class="header-arrow"></div>
        </label>
        <input class="header_check" type="checkbox" {% if (typeof expanded
               !== 'undefined' && expanded == false) { %} {% } else { %}checked="true"{% } %} id="header_{%=key%}">
        <div class="section" data-section="{%=key%}"></div>

    </script>
    <script id="easyticket-property" type="text/html">
        <div
            class="form-group {% if (typeof col !== 'undefined' && col != false) { %} col-sm-{%=col%} d-inline-block {% } else { %}row{% } %}"
            data-key="{%=key%}" {% if (typeof group
            !== 'undefined' && group != null) { %}data-group="{%=group%}" {% } %}>

        {% if (typeof name !== 'undefined' && name != false) { %}<label
            class="{% if (typeof inline === 'undefined' ) { %}col-sm-4{% } %} control-label"
            for="input-model">{%=name%}</label>{% } %}

        <div
            class="{% if (typeof inline === 'undefined') { %}col-sm-{% if (typeof name !== 'undefined' && name != false) { %}8{% } else { %}12{% } } %} input"></div>

        </div>

    </script>
    <script id="easyticket-input-autocompletelist" type="text/html">
        <div>
            <input name="{%=key%}" type="text" class="form-control"/>

            <div class="form-control autocomplete-list" style="min=height: 150px; overflow: auto;">
                <div id="featured-product43"><i class="la la-close"></i> MacBook
                    <input name="product[]" value="43" type="hidden">
                </div>
                <div id="featured-product40"><i class="la la-close"></i> iPhone
                    <input name="product[]" value="40" type="hidden">
                </div>
                <div id="featured-product42"><i class="la la-close"></i> Apple Cinema 30"
                    <input name="product[]" value="42" type="hidden">
                </div>
                <div id="featured-product30"><i class="la la-close"></i> Canon EOS 5D
                    <input name="product[]" value="30" type="hidden">
                </div>
            </div>
        </div>

    </script>
    <!--// end templates -->
    <!-- export html modal-->
    <div class="modal fade" id="textarea-modal" tabindex="-1" role="dialog" aria-labelledby="textarea-modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title text-primary"><i class="la la-lg la-save"></i> Export html</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small><i class="la la-close"></i></small></span>
                    </button>
                </div>
                <div class="modal-body">
                    <textarea rows="25" cols="150" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i
                            class="la la-close"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- message modal-->
    <div class="modal fade" id="message-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title text-primary"><i class="la la-lg la-comment"></i> EasyticketJs</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small><i class="la la-close"></i></small></span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Page was successfully saved!.</p>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-primary">Ok</button> -->
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i
                            class="la la-close"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- new page modal-->
    <div class="modal fade" id="new-page-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form>
                <div class="modal-content">
                    <div class="modal-header">
                        <p class="modal-title text-primary"><i class="la la-lg la-file"></i> New page</p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><small><i class="la la-close"></i></small></span>
                        </button>
                    </div>
                    <div class="modal-body text">
                        <div class="form-group row" data-key="type">
                            <label class="col-sm-3 control-label">
                                Template
                                <abbr class="badge badge-pill badge-secondary"
                                      title="This template will be used as a start">?</abbr>
                            </label>
                            <div class="col-sm-9 input">
                                <div>
                                    <select class="form-control custom-select" name="startTemplateUrl">
                                        <option value="new-page-blank-template.html">Blank Template</option>
                                        <option value="demo/narrow-jumbotron/index.html">Narrow jumbotron</option>
                                        <option value="demo/album/index.html">Album</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" data-key="href">
                            <label class="col-sm-3 control-label">Page name</label>
                            <div class="col-sm-9 input">
                                <div>
                                    <input name="title" type="text" class="form-control" placeholder="My page" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" data-key="href">
                            <label class="col-sm-3 control-label">File name</label>
                            <div class="col-sm-9 input">
                                <div>
                                    <input name="fileName" type="text" class="form-control" placeholder="my-page.html"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-lg" type="submit"><i class="la la-check"></i> Create page
                        </button>
                        <button class="btn btn-secondary btn-lg" type="reset" data-dismiss="modal"><i
                                class="la la-close"></i> Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- jquery-->
<script src="{{ URL::asset('EasyticketJs-master/js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('EasyticketJs-master/js/jquery.hotkeys.js') }}"></script>
<!-- bootstrap-->
<script src="{{ URL::asset('EasyticketJs-master/js/popper.min.js') }}"></script>
<script src="{{ URL::asset('EasyticketJs-master/js/bootstrap.min.js') }}"></script>
<!-- builder code-->
<script src="{{ URL::asset('EasyticketJs-master/libs/builder/builder.js') }}"></script>
<!-- undo manager-->
<script src="{{ URL::asset('EasyticketJs-master/libs/builder/undo.js') }}"></script>
<!-- inputs-->
<script src="{{ URL::asset('EasyticketJs-master/libs/builder/inputs.js') }}"></script>
<!-- bootstrap colorpicker //uncomment bellow scripts to enable -->
<!-- components-->
<script src="{{ URL::asset('EasyticketJs-master/libs/builder/components-bootstrap4.js') }}"></script>
<script src="{{ URL::asset('EasyticketJs-master/libs/builder/components-widgets.js') }}"></script>
<!-- blocks-->
<script src="{{ URL::asset('EasyticketJs-master/libs/builder/blocks-bootstrap4.js') }}"></script>
<!-- plugins -->
<!-- code mirror - code editor syntax highlight -->
<link href="{{ URL::asset('EasyticketJs-master/libs/codemirror/lib/codemirror.css') }}" rel="stylesheet"/>
<link href="{{ URL::asset('EasyticketJs-master/libs/codemirror/theme/material.css') }}" rel="stylesheet"/>
<script src="{{ URL::asset('EasyticketJs-master/libs/codemirror/lib/codemirror.js') }}"></script>
<script src="{{ URL::asset('EasyticketJs-master/libs/codemirror/lib/xml.js') }}"></script>
<script src="{{ URL::asset('EasyticketJs-master/libs/codemirror/lib/formatting.js') }}"></script>
<script src="{{ URL::asset('EasyticketJs-master/libs/builder/plugin-codemirror.js') }}"></script>
<script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://unpkg.com/cropperjs/dist/cropper.js" crossorigin="anonymous"></script>
<script src="https://fengyuanchen.github.io/jquery-cropper/js/jquery-cropper.js"></script>

<script>
    Easyticket.ComponentsGroup['Components'].push("html/registration_reference")
    Easyticket.ComponentsGroup['Components'].push("html/function")
    Easyticket.ComponentsGroup['Components'].push("html/first_name")
    Easyticket.ComponentsGroup['Components'].push("html/last_name")
    Easyticket.ComponentsGroup['Components'].push("html/full_name")
    Easyticket.Components.extend("_base", "html/first_name", {
        nodes: ["p"],
        name:  "First Name",
        image: "../../../../../../EasyticketJs-master/libs/builder/icons/heading.svg",
        html: "<p  class='first_name' key='first_name'>First Name</p>",
        properties: [{
            name: "Text align",
            key: "text-align",
            htmlAttr: "class",
            inputtype: SelectInput,
            validValues: ["", "text-left", "text-center", "text-right"],
            inputtype: RadioButtonInput,
            data: {
                extraclass:"btn-group-sm btn-group-fullwidth",
                options: [{
                    value: "",
                    icon:"la la-close",
                    //text: "None",
                    title: "None",
                    checked:true,
                }, {
                    value: "left",
                    //text: "Left",
                    title: "text-left",
                    icon:"la la-align-left",
                    checked:false,
                }, {
                    value: "text-center",
                    //text: "Center",
                    title: "Center",
                    icon:"la la-align-center",
                    checked:false,
                }, {
                    value: "text-right",
                    //text: "Right",
                    title: "Right",
                    icon:"la la-align-right",
                    checked:false,
                }],
            },
        }]
    });

    Easyticket.Components.extend("_base", "html/registration_reference", {
        nodes: ["p"],
        name:  "Registration Reference",
        image: "../../../../../../EasyticketJs-master/libs/builder/icons/heading.svg",
        html: "<p  class='registration_reference' key='registration_reference'>EWIFH34H</p>",
        properties: [{
            name: "Text align",
            key: "text-align",
            htmlAttr: "class",
            inputtype: SelectInput,
            validValues: ["", "text-left", "text-center", "text-right"],
            inputtype: RadioButtonInput,
            data: {
                extraclass:"btn-group-sm btn-group-fullwidth",
                options: [{
                    value: "",
                    icon:"la la-close",
                    //text: "None",
                    title: "None",
                    checked:true,
                }, {
                    value: "left",
                    //text: "Left",
                    title: "text-left",
                    icon:"la la-align-left",
                    checked:false,
                }, {
                    value: "text-center",
                    //text: "Center",
                    title: "Center",
                    icon:"la la-align-center",
                    checked:false,
                }, {
                    value: "text-right",
                    //text: "Right",
                    title: "Right",
                    icon:"la la-align-right",
                    checked:false,
                }],
            },
        }]
    });

    Easyticket.Components.extend("_base", "html/function", {
        nodes: ["p"],
        name:  "Function",
        image: "../../../../../../EasyticketJs-master/libs/builder/icons/heading.svg",
        html: "<p  class='function' key='function'>Function</p>",
        properties: [{
            name: "Text align",
            key: "text-align",
            htmlAttr: "class",
            inputtype: SelectInput,
            validValues: ["", "text-left", "text-center", "text-right"],
            inputtype: RadioButtonInput,
            data: {
                extraclass:"btn-group-sm btn-group-fullwidth",
                options: [{
                    value: "",
                    icon:"la la-close",
                    //text: "None",
                    title: "None",
                    checked:true,
                }, {
                    value: "left",
                    //text: "Left",
                    title: "text-left",
                    icon:"la la-align-left",
                    checked:false,
                }, {
                    value: "text-center",
                    //text: "Center",
                    title: "Center",
                    icon:"la la-align-center",
                    checked:false,
                }, {
                    value: "text-right",
                    //text: "Right",
                    title: "Right",
                    icon:"la la-align-right",
                    checked:false,
                }],
            },
        }]
    });


    Easyticket.Components.extend("_base", "html/last_name", {
        nodes: ["p"],
        name:  "Last Name",
        image: "../../../../../../EasyticketJs-master/libs/builder/icons/heading.svg",
        html: "<p  class='last_name' key='last_name'>Last Name</p>",
        properties: [{
            name: "Text align",
            key: "text-align",
            htmlAttr: "class",
            inputtype: SelectInput,
            validValues: ["", "text-left", "text-center", "text-right"],
            inputtype: RadioButtonInput,
            data: {
                extraclass:"btn-group-sm btn-group-fullwidth",
                options: [{
                    value: "",
                    icon:"la la-close",
                    //text: "None",
                    title: "None",
                    checked:true,
                }, {
                    value: "left",
                    //text: "Left",
                    title: "text-left",
                    icon:"la la-align-left",
                    checked:false,
                }, {
                    value: "text-center",
                    //text: "Center",
                    title: "Center",
                    icon:"la la-align-center",
                    checked:false,
                }, {
                    value: "text-right",
                    //text: "Right",
                    title: "Right",
                    icon:"la la-align-right",
                    checked:false,
                }],
            },
        }]
    });

    Easyticket.Components.extend("_base", "html/full_name", {
        nodes: ["p"],
        name:  "Full Name",
        image: "../../../../../../EasyticketJs-master/libs/builder/icons/heading.svg",
        html: "<p  class='full_name' key='full_name'>Full Name</p>",
        properties: [{
            name: "Text align",
            key: "text-align",
            htmlAttr: "class",
            inputtype: SelectInput,
            validValues: ["", "text-left", "text-center", "text-right"],
            inputtype: RadioButtonInput,
            data: {
                extraclass:"btn-group-sm btn-group-fullwidth",
                options: [{
                    value: "",
                    icon:"la la-close",
                    //text: "None",
                    title: "None",
                    checked:true,
                }, {
                    value: "left",
                    //text: "Left",
                    title: "text-left",
                    icon:"la la-align-left",
                    checked:false,
                }, {
                    value: "text-center",
                    //text: "Center",
                    title: "Center",
                    icon:"la la-align-center",
                    checked:false,
                }, {
                    value: "text-right",
                    //text: "Right",
                    title: "Right",
                    icon:"la la-align-right",
                    checked:false,
                }],
            },
        }]
    });
</script>
@foreach ($questions as $item)
    @php
        $title = strtolower(str_replace(' ', '_', $item['title']))
        // comeback here
    @endphp

    <script>
        Easyticket.ComponentsGroup['Components'].push("html/{{ $title }}")
        Easyticket.Components.extend("_base", "html/{{ $title }}", {
            nodes: ["p"],
            name:  "{{ $item['title'] }}",
            image: "../../../../../../EasyticketJs-master/libs/builder/icons/heading.svg",
            html: "<p id='{{ $title }}' class='{{ $title }}' key='{{ $title }}'>{{ $item['title'] }}</p>",
            properties: [{
                name: "Text align",
                key: "text-align",
                htmlAttr: "class",
                inputtype: SelectInput,
                validValues: ["", "text-left", "text-center", "text-right"],
                inputtype: RadioButtonInput,
                data: {
                    extraclass:"btn-group-sm btn-group-fullwidth",
                    options: [{
                        value: "",
                        icon:"la la-close",
                        //text: "None",
                        title: "None",
                        checked:true,
                    }, {
                        value: "left",
                        //text: "Left",
                        title: "text-left",
                        icon:"la la-align-left",
                        checked:false,
                    }, {
                        value: "text-center",
                        //text: "Center",
                        title: "Center",
                        icon:"la la-align-center",
                        checked:false,
                    }, {
                        value: "text-right",
                        //text: "Right",
                        title: "Right",
                        icon:"la la-align-right",
                        checked:false,
                    }],
                },
            }]
        });
    </script>
@endforeach

<!-- jszip - download page as zip -->
<!-- script src="libs/jszip/jszip.min.js"></script>
   <script src="libs/builder/plugin-jszip.js"></script -->
<script>
    $(document).ready(function()
    {
        var $image = $('#image');

        $image.cropper({
            aspectRatio: 16 / 9,
            crop: function(event) {
                console.log(event.detail.x);
                console.log(event.detail.y);
                console.log(event.detail.width);
                console.log(event.detail.height);
                console.log(event.detail.rotate);
                console.log(event.detail.scaleX);
                console.log(event.detail.scaleY);
            }
        });
        //if url has #no-right-panel set one panel demo
        if (window.location.hash.indexOf("no-right-panel") != -1)
        {
            $("#easyticket-builder").addClass("no-right-panel");
            $(".component-properties-tab").show();
            Easyticket.Components.componentPropertiesElement = "#left-panel .component-properties";
        } else
        {
            $(".component-properties-tab").hide();
        }

        Easyticket.Builder.init('demo/narrow-jumbotron/index.html', function() {
            //run code after page/iframe is loaded
        });

        Easyticket.Gui.init();
        Easyticket.FileManager.init();
        Easyticket.FileManager.addPages(
            [
                    {{--  {name:"narrow-jumbotron", title:"badge 1",  url: "{{ URL::asset('EasyticketJs-master/demo/narrow-jumbotron/index.html') }}", assets: ["{{ URL::asset('EasyticketJs-master/demo/narrow-jumbotron/narrow-jumbotron.css') }}"]},


                    {name:"album", title:"badge 2",  url: "{{ URL::asset('EasyticketJs-master/demo/album/index.html') }}", folder:"content", assets: ["{{ URL::asset('EasyticketJs-master/demo/album/album.css') }}"]},

                            {name:"blog", title:"badge 2",  url: "{{ URL::asset('EasyticketJs-master/demo/blog/index.html') }}", folder:"content", assets: ["{{ URL::asset('EasyticketJs-master/demo/blog/blog.css') }}"]},

                            {name:"carousel", title:"badge 3",  url: "{{ URL::asset('EasyticketJs-master/demo/carousel/index.html') }}", folder:"content", assets: ["{{ URL::asset('EasyticketJs-master/demo/carousel/carousel.css') }}"]},

                            {name:"offcanvas", title:"badge 4",  url: "{{ URL::asset('EasyticketJs-master/demo/offcanvas/index.html') }}", folder:"content", assets: ["{{ URL::asset('EasyticketJs-master/demo/offcanvas/offcanvas.css') }}","{{ URL::asset('EasyticketJs-master/demo/offcanvas/offcanvas.js') }}"]},

                           {name:"pricing", title:"badge 5",  url: "{{ URL::asset('EasyticketJs-master/demo/pricing/index.html') }}", folder:"More..", assets: ["{{ URL::asset('EasyticketJs-master/demo/pricing/pricing.css') }}"]},  --}}
                    <?php
                    if(isset($event_badge) && $event_badge != NULL && $event_badge != " "){
                        $pathInfo = $event_badge->fileName .'?width='. $badge->width . '&height=' . $badge->height ;

                    } else {
                        $url = 'user_content/index.html?width=' . $badge->width . '&height=' . $badge->height;
                        $pathInfo = $url;
                    }
                    ?>

                {name:"product", title:"badge 6",  url: "{{ URL::asset($pathInfo) }}", folder:"Badge", assets: ["{{ URL::asset('EasyticketJs-master/demo/product/product.css') }}"]},



            ]);



        Easyticket.FileManager.loadPage("product");
        {{-- @endif --}}
    });
</script>
</body>
</html>
