<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials._head')
    @include('partials._styles')
</head>
<body>
    @include('partials._sidebar')
    
    <!-- Main Content -->
    <div class="main-content">
        @include('partials._header')
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @include('partials._alerts')
            @include('partials._breadcrumbs')
            @yield('content')
        </div>
        
        @include('partials._footer')
    </div>
    
    @yield('modals')
    @include('partials._scripts')
</body>
</html>

