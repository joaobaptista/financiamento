@php
    $supportEmail = (string) config('mail.from.address');
@endphp

<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
<p style="margin: 0 0 8px;">
Se precisar de ajuda, responda este email ou fale com a gente em
<a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.
</p>
<p style="margin: 0 0 8px;">Abraços,<br><strong>Origo</strong></p>
<p style="margin: 0;">© {{ date('Y') }} Origo. {{ __('All rights reserved.') }}</p>
</td>
</tr>
</table>
</td>
</tr>
