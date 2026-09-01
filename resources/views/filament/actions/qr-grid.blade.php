<div>
    <div
            id="page"
            style="
            display: {{ count($items) === 1 ? 'flex' : 'grid' }};
            {{ count($items) === 1
                ? 'align-items:center; justify-content:center; min-height:200px;'
                : 'grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:1rem;'
            }}
            margin: 0 auto;
            padding: 1rem;
            box-sizing: border-box;
            {{ count($items) > 4 ? 'max-height:50vh; overflow-y:auto;' : '' }}
        "
    >
        @foreach ($items as $item)
            <div
                    style="
                    position: relative;
                    border: 1px solid #0a0a0a;
                    border-radius: 0.5rem;
                    padding: 1rem;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    {{ count($items) === 1 ? 'width:100%; max-width:400px;' : '' }}
                "
            >
                <img
                        src="{{ $item['src'] }}"
                        alt="QR: {{ $item['label'] }}"
                        style="width:100%; height:auto; display:block; margin-bottom:0.5rem;"
                >
                <div style="text-align:center;">{{ $item['label'] }}</div>

                <div
                        style="
                        position:absolute;
                        top:0;
                        left:0;
                        width:100%;
                        height:100%;
                        background: rgba(0,0,0,0.5);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        opacity:0;
                        transition: opacity 0.3s;
                    "
                        onmouseover="this.style.opacity=1"
                        onmouseout="this.style.opacity=0"
                >
                    <a
                            href="{{ $item['src'] }}"
                            download="{{ $item['label'] }}"
                            style="
                            background:#2563eb;
                            color:white;
                            padding:10px 16px;
                            border-radius:8px;
                            cursor:pointer;
                            font-size:14px;
                            text-decoration:none;
                        "
                    >
                        دانلود
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
