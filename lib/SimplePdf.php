<?php
/**
 * Minimal PDF generator for report export (no external dependencies).
 */
class SimplePdf
{
    private array $pages = [];
    private int $pageIndex = -1;
    private float $y = 750;
    private float $margin = 50;
    private float $lineHeight = 14;

    public function __construct()
    {
        $this->addPage();
    }

    public function addPage(): void
    {
        $this->pages[] = ['content' => ''];
        $this->pageIndex++;
        $this->y = 750;
    }

    public function setTitle(string $title): void
    {
        $this->addText($title, 16, true);
        $this->y -= 10;
    }

    public function addText(string $text, int $size = 11, bool $bold = false): void
    {
        if ($this->y < 60) {
            $this->addPage();
        }
        $font = $bold ? '/F2' : '/F1';
        $escaped = $this->escape($text);
        $this->pages[$this->pageIndex]['content'] .= "BT {$font} {$size} Tf {$this->margin} {$this->y} Td ({$escaped}) Tj ET\n";
        $this->y -= $this->lineHeight + ($size > 12 ? 4 : 0);
    }

    public function addTable(array $headers, array $rows): void
    {
        $this->addText(implode(' | ', $headers), 10, true);
        foreach ($rows as $row) {
            $this->addText(implode(' | ', array_map('strval', $row)), 9);
        }
    }

    public function output(string $filename): void
    {
        $pdf = $this->build();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private function escape(string $text): string
    {
        $text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text) ?: $text;
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function build(): string
    {
        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $pageRefs = [];
        $objNum = 3;

        foreach ($this->pages as $i => $page) {
            $contentObj = $objNum++;
            $pageObj = $objNum++;
            $pageRefs[] = $pageObj . ' 0 R';
            $content = $page['content'];
            $objects[] = '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
            $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> /Contents ' . $contentObj . ' 0 R >>';
        }

        $kids = implode(' ', $pageRefs);
        array_splice($objects, 1, 0, ["<< /Type /Pages /Kids [{$kids}] /Count " . count($this->pages) . ' >>']);
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $i => $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n" . $obj . "\nendobj\n";
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF";

        return $pdf;
    }
}
