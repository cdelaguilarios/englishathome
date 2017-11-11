@extends("layouts.master")
@section("titulo", "Motor de reportes")

@section("section_style")
@endsection

@section("section_script")
@endsection

@section("breadcrumb")
<li class="active">Motor de reportes</li>
@endsection

@section("content")
@foreach ($entidades as $k => $v)
{{ Form::radio('entidad', $k, false, ["id"=>$k]) }} {{ Form::label($k, $v[0]) }}<br>
@endforeach
@endsection
