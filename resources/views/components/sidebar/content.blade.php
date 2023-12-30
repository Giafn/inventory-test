<x-perfect-scrollbar as="nav" aria-label="main" class="flex flex-col flex-1 gap-4 px-3">
@foreach (config('navmenu') as $item)
    @if (Auth::user()->hasPermission($item['code']))
        @if (count($item['child']) > 0)
            <x-sidebar.dropdown title="{{ $item['name'] }}" :active="Str::startsWith(request()->route()->uri(), $item['route'])">
                <x-slot name="icon">
                    @svg($item['icon'], 'flex-shrink-0 w-6 h-6')
                </x-slot>
                @foreach ($item['child'] as $subItem)
                    <x-sidebar.sublink title="{{ $subItem['name'] }}" href="{{ Route::has($item['route']) ? route($subItem['route']) : '#' }}" :active="request()->routeIs($subItem['route'])"/>
                @endforeach
            </x-sidebar.dropdown>
        @else
            <x-sidebar.link title="{{ $item['name'] }}" href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" :isActive="request()->routeIs($item['route'])">
                <x-slot name="icon">
                    @svg($item['icon'], 'flex-shrink-0 w-6 h-6')
                </x-slot>
            </x-sidebar.link>
        @endif
    @endif
@endforeach
</x-perfect-scrollbar>
