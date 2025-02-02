<?php

require __DIR__.'/vendor/autoload.php';

$builder = new \PhpParser\BuilderFactory;

$s = $builder->val()

$code = file_get_contents('snippet.php');

$parser = (new PhpParser\ParserFactory)->createForNewestSupportedVersion();

$stmts = $parser->parse($code);

$prettyPrinter = new \PhpParser\PrettyPrinter\Standard;
echo $prettyPrinter->prettyPrintFile($stmts);
