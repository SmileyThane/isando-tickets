
<script type="text/php">
    if (isset($pdf)) {
        $x = 550;
        $y = 800;
        $text = "{PAGE_NUM} of {PAGE_COUNT}";
        $font = null;
        $size = 12;
        $color = array(255,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        // header
        $pdf->page_text(250,10,'HEADER: GGG',$font,$size,$color,$word_space,$char_space,$angle);
        // footer
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>
