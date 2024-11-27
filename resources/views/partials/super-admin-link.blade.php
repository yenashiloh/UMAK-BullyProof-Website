<meta
content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
name="viewport"
/>
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="logout-route" content="">
<link
rel="icon"
href="../../../../assets/img/logo-3.png"
type="image/x-icon"
/>

<!-- Fonts and icons -->
<script src="../../../../assets/js/plugin/webfont/webfont.min.js"></script>
<script>
WebFont.load({
  google: { families: ["Public Sans:300,400,500,600,700"] },
  custom: {
    families: [
      "Font Awesome 5 Solid",
      "Font Awesome 5 Regular",
      "Font Awesome 5 Brands",
      "simple-line-icons",
    ],
    urls: ["../../../../assets/css/fonts.min.css"],
  },
  active: function () {
    sessionStorage.fonts = true;
  },
});
</script>

<!-- CSS Files -->
<link rel="stylesheet" href="../../../../assets/css/bootstrap.min.css" />
<link rel="stylesheet" href="../../../../assets/css/plugins.min.css" />
<link rel="stylesheet" href="../../../../assets/css/kaiadmin.min.css" />
<link rel="stylesheet" href="../../../../assets/css/style.css" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

