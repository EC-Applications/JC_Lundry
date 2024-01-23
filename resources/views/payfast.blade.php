<html>
<head></head>
<body>
    <form action="{{ $redirectUrl }}" method="post" id='PayFast_payment_form' name="from1">
    @foreach ($requestParams as $a => $b)
    {{-- {{ dd(htmlentities($b)) }} --}}
        <input type="hidden" name="{{ $a }}" value="{{ $b }}">
    @endforeach
    <script type='text/javascript'>
     document.from1.submit();
    </script>
    </form>
</body>
</html>
