<hr width=50% align='center'>
<div style="text-align: center;">
    <span style="font-size: larger; "><b><{$title}></b></span><br>
    <b><i><{$subtitle}></i></b><br>
    <span style="font-size: larger; "><{$subsubtitle}></span><br>
</div>
<hr width=50% align='center'>
<{if count($sections) gt 0}>
    <table class='outer' border='0' cellspacing='5' cellpadding='0' align="center" width="100%">
        <tr>
            <!-- Start sections loop -->
            <{foreach item=section from=$sections}>
            <{if $section.title}>
                <{if $section.id == 0}>
                    <td class="itemHead" valign="top"><a href="<{$xoops_url}>/modules/myconference/program.php?cid=<{$cid}>"><b><{$section.title}></b></a></td>
                <{else}>
                    <td class="itemHead" valign="top"><a href="<{$xoops_url}>/modules/myconference/index.php?sid=<{$section.id}>"><b><{$section.title}></b></a></td>
                <{/if}>
            <{/if}>
            <{if $section.count % 5 == 0}>
        </tr>
        <tr>
            <{/if}>
            <{/foreach}>
            <!-- End sections loop -->
        </tr>
    </table>
<{/if}>
<table class='outer' border='0' cellspacing='5' cellpadding='0' align="center" width="100%">
    <tr>
        <td colspan=2 class=head align='center'><{$ttitle}></td>
    </tr>
    <tr>
        <td class=odd width='<{$width}>'><{$lang_summary}></td>
        <td class=odd>
            <i><{$summary}></i>
        </td>
    </tr>
</table>

<br><br>
