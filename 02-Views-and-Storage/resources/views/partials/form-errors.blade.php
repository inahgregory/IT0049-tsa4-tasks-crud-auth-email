@if ($errors->any())
<div class="validation-summary" role="alert"><h2>Please check your details</h2><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

