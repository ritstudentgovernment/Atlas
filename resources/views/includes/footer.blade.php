<script src="{{ mix('/js/app.js') }}" async></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= $map["api_key"] ?>&callback=initMap" async></script>
<script>
    function loaded() {
        @if(session()->has('api_key'))
            // Set api key in headers
            <?= session(['api_key' => \JWTAuth::fromUser(auth()->user())]); // Refresh API Key ?>
            window.axios.defaults.headers.common['Authorization'] = "bearer <?= session('api_key') ?>";
        @endif
    }
</script>