<?php
class TemplatesService {

    public static function getElementFromHTML(string $filename)
    {
        return file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/template/$filename");
    }

    public static function getElementFromHTMLWithParams(string $filename, array $args)
    {

        $text = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/template/$filename");

        foreach ($args as $key => $value) {
            $i = strpos($text, $key);

            if ($i == -1)
                continue;

            $text = str_replace("[[$key]]", $value, $text);
        }
        return $text;
    }
}
