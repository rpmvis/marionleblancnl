<!doctype html>

<html>
<form method="post" action="/regex/" accept-charset="UTF-8">
<textarea name="test_string"  rows="15" cols="60">
@if (!empty($test_string)){{ $test_string }}
    @else{{"Enter test string here..."}}
    @endif
</textarea>

<textarea name="pattern"  rows="3" cols="50">
@if (!empty($pattern))
{{$pattern}}
@endif
</textarea>

<input type="submit" value="Submit">

<label>
    <span>aantal matches:</span> {{ $matches_count }}
</label>

{{-- <textarea readonly --}}
<textarea name="matches"  rows="15" cols="60">
@if (!empty($matches))
@foreach ($matches as $match)
{{ $match }}
@endforeach
@else{{"Regex matches come here..."}}
@endif
</textarea>

{{-- <textarea readonly --}}
<textarea name="groups"  rows="15" cols="60">
@if (!empty($groups))
@foreach ($groups as $group)
{{$group}}
@endforeach
@else{{"Groups from regex matches come here..."}}
@endif
</textarea>

</form>
</html>
