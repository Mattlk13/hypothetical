@extends('dashboard.core')

@section('dashboard-body')
    @foreach(App\Models\DashboardMenu::$menu as $menu_item)
        @if(is_array($menu_item[1]))
            @foreach($menu_item[1] as $submenu_item)
                <li class="list-group-item">
                    <a href="{{ url('/dashboard/' . $submenu_item[1]) }}">
                        {{ $menu_item[0] }}: {{ $submenu_item[0] }}
                    </a>
                </li>
            @endforeach
        @else
            <li class="list-group-item">
                <a href="{{ url('/dashboard/' . $menu_item[1]) }}">
                    {{ $menu_item[0] }}
                </a>
            </li>
        @endif
    @endforeach
@endsection
