-use yii\widgets\DetailView<?= "\n" ?>

-
  $attributes = [
<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "    '" . $name . "',\n";
        } else {
            echo "    //'" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "    '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "    //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
  ]

<?= "!=" ?>DetailView::widget(['model' => $model,'attributes' => $attributes ])<?= "\n" ?>
