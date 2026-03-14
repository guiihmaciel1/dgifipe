@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs($route . '*');
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>',
        'calculator' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25v-.008Zm2.25-4.5h.008v.008H10.5v-.008Zm0 2.25h.008v.008H10.5v-.008Zm0 2.25h.008v.008H10.5v-.008Zm2.25-4.5h.008v.008H12.75v-.008Zm0 2.25h.008v.008H12.75v-.008Zm2.25-2.25h.008v.008H15v-.008Zm0 2.25h.008v.008H15v-.008ZM5.25 3.75h13.5A2.25 2.25 0 0 1 21 6v12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18V6a2.25 2.25 0 0 1 2.25-2.25Z"/>',
        'radar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
        'upload' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>',
        'building' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M3.75 3v18m4.5-18v18m4.5-18v18m4.5-18v18M5.25 3h13.5M5.25 21h13.5M8.25 6.75h.008v.008H8.25V6.75ZM8.25 9.75h.008v.008H8.25V9.75ZM8.25 12.75h.008v.008H8.25v-.008ZM8.25 15.75h.008v.008H8.25v-.008ZM11.25 6.75h.008v.008h-.008V6.75ZM11.25 9.75h.008v.008h-.008V9.75ZM11.25 12.75h.008v.008h-.008v-.008ZM11.25 15.75h.008v.008h-.008v-.008ZM14.25 6.75h.008v.008h-.008V6.75ZM14.25 9.75h.008v.008h-.008V9.75ZM14.25 12.75h.008v.008h-.008v-.008ZM14.25 15.75h.008v.008h-.008v-.008Z"/>',
    ];
@endphp

<a href="{{ route($route) }}" class="flex flex-col items-center gap-0.5 px-3 py-1 min-w-0">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
         stroke="currentColor"
         class="w-6 h-6 {{ $isActive ? 'text-apple-blue' : 'text-apple-muted' }}">
        {!! $icons[$icon] ?? '' !!}
    </svg>
    <span class="text-[10px] {{ $isActive ? 'text-apple-blue font-medium' : 'text-apple-muted' }}">{{ $label }}</span>
</a>
