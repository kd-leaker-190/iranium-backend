<div>
    <div id="reader" style="width:100%;margin:0 auto;border-radius:12px;overflow:hidden"></div>
    <div class="mt-4 text-center p-4">
        <span class="text-gray-500">کپی نام با اسکن</span>
        <span id="output" class="text-blue-500 font-bold"></span>
    </div>


    <script src="{{ asset('js/html5-qrcode-bundle.js') }}"></script>
    <livewire:scripts />
    <script>
        const html5QrCode = new Html5Qrcode("reader");

        html5QrCode.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: 250
            },
            (decodedText) => {
                json = JSON.parse(decodedText);
                document.getElementById('data.team_id').value = decodedText;
                document.getElementById('output').innerText = json.title;
            }
        );
    </script>
</div>
