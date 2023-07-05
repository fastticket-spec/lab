<head>
    <script src="{{ asset('EasyticketJs-master/js/jquery.min.js') }}"></script>
    <script src="{{ asset('EasyticketJs-master/js/jquery.hotkeys.js') }}"></script>
{{--      <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>--}}
    <script src="{{ asset('js/html2canvas.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.js "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.1.1/jspdf.umd.min.js"></script>
</head>

{!! $html_data !!}

<img id="hold1" src="">
<img id="hold2" src="">

<script>
    let collection = document.getElementsByTagName("p");
    document.getElementById("badgeContainer").style.height = {{$badge->height * 38}} + 'px';
    document.getElementById("badgeContainer").style.width = {{$badge->width * 38}} + 'px';
    for (let i = 0; i < collection.length; i++) {
        console.log(collection[i].innerHTML = collection[i].innerHTML.replace('&nbsp;', ' '));
        console.log(collection[i].style.wordWrap = 'normal');
        console.log(collection[i].style.letterSpacing = 'normal');
    }

    function isUnicode(str) {
        let letters = [];
        for (let i = 0; i <= str.length; i++) {
            letters[i] = str.substring((i - 1), i);
            if (letters[i].charCodeAt() > 255) {
                return true;
            }
        }
        return false;
    }

    let source = document.getElementById('badgeContainer')
    source.classList.remove("container");

    @if($type == 'split')
    const myTimeout = setTimeout(gpdf, 5000);

    html2canvas(source, {
        useCORS: true,
        allowTaint: false,
        x: 0,
        y: 0,
        width: source.clientWidth,
        height: (source.clientHeight / 2),
        scale: 35
    }).then(canvas => {
        document.querySelector("#hold1").src = canvas.toDataURL('image/jpeg')
    });

    html2canvas(source, {
        useCORS: true,
        allowTaint: false,
        x: 0,
        y: 238,
        width: source.clientWidth,
        height: source.clientHeight / 2,
        scale: 35
    }).then(canvas => {
        document.querySelector("#hold2").src = canvas.toDataURL('image/jpeg')
    });


    function gpdf() {
        const {jsPDF} = window.jspdf;
        const imgWidth = source.clientWidth
        const imgHeight = source.clientHeight;

        let pdf = new jsPDF('L', 'px', [imgWidth, imgHeight / 2]); // A4 size page of PDF

        let s = document.querySelector("#hold1").src

        pdf.addImage(s, 'JPEG', 0, 0, imgWidth, imgHeight / 2)
        pdf.addPage();
        pdf.addImage(document.querySelector("#hold2").src, 'JPEG', 0, 0, imgWidth, imgHeight / 2)

        pdf.save('MYPdf.pdf');
    }

    @else
    html2canvas(source, {useCORS: true, allowTaint: true, scale: 5}).then(canvas => {
        // Few necessary setting options
        const imgWidth = {{ $badge->width }};
        const imgHeight = {{ $badge->height }};

        const contentDataURL = canvas.toDataURL('image/jpeg')

        document.body.appendChild(canvas);
        const {jsPDF} = window.jspdf;

        let pdf = new jsPDF('p', 'cm', [imgWidth, imgHeight]); // A4 size page of PDF
        pdf.addImage(contentDataURL, 'JPEG', 0, 0, imgWidth, imgHeight)
        // let url_string = window.location.href
        // url_string = url_string.replace('&amp;', '&')
        // const url = new URL(url_string);
        // var preview = url.searchParams.get("preview");
        // var print = url.searchParams.get("print");
        // var download = url.searchParams.get("download");

        pdf.save('MYPdf.pdf');


    });
    @endif


</script>

