<!doctype html>

<html>
    <form method="post" action="/html_helper/" accept-charset="UTF-8">
        <textarea name="html_input"  rows="40" cols="60">
@if (!empty($html_input)) {{  $html_input }}
@else Enter html here...
@endif
        </textarea>
        <input type="submit" value="Submit">

{{--<textarea readonly>        --}}
<textarea name="html_output"  rows="40" cols="60">
@if (!empty($html_output))
{{{ $html_output }}}
@else Result after submit comes here...
@endif
</textarea>
    </form>
</html>
