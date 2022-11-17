<style>
    blockquote {
        font-style: inherit;
    }
</style>


<div class="row">

    <div class="col-lg-9">
        <div class="documentation">
            <?php

            $path    = BASEPATH . '/pages/docs';
            $files = array_diff(scandir($path), array('.', '..'));
            // dump($files, true);

            // $headers = [];

            foreach ($files as $file) {
                $text = file_get_contents($path . "/" . $file);
                $parsedown = new Parsedown;
                // $header = $parsedown->header;
                // $header = array_filter($header, function ($v) {
                //     return $v['level'] <= 2;
                // });
                // $headers = array_merge($headers, $header);
                echo $parsedown->text($text);
            }


            ?>

        </div>
        <div class="col-lg-3 d-none d-lg-block">
            <div class="on-this-page-nav" id="on-this-page-nav">
                <div class="content">
                    <div class="title">On this page</div>
                    <a href="#beispielaktivitäten">Beispielaktivitäten</a>
                </div>
            </div>
        </div>

    </div>
</div>