<script src="{{ mix('/js/app.js') }}" async></script>
<script>
    function loaded() {
        @if(session()->has('api_key'))
            // Set api key in headers
            <?= session(['api_key' => \JWTAuth::fromUser(auth()->user())]); // Refresh API Key ?>
            window.axios.defaults.headers.common['Authorization'] = "bearer <?= session('api_key') ?>";
        @endif
    }
</script>