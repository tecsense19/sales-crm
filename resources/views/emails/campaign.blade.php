@php
    $isHtml = strip_tags($body) !== $body;
@endphp

@if($isHtml && (str_contains($body, '<html') || str_contains($body, '<body')))
    {!! $body !!}
@else
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                padding: 20px;
                max-width: 600px;
                margin: 0 auto;
            }
            p {
                margin-top: 0;
                margin-bottom: 12px;
            }
            p:last-child {
                margin-bottom: 0;
            }
        </style>
    </head>
    <body>
        @if($isHtml)
            {!! $body !!}
        @else
            {!! nl2br(e($body)) !!}
        @endif
    </body>
    </html>
@endif
