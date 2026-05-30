@php
    $isHtml = strip_tags($body) !== $body;
@endphp

@if($isHtml)
    @if(str_contains($body, '<html') || str_contains($body, '<body'))
        {!! $body !!}
    @else
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0;">
            {!! $body !!}
        </body>
        </html>
    @endif
@else
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                padding: 20px;
                max-width: 600px;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
        {!! nl2br(e($body)) !!}
    </body>
    </html>
@endif
