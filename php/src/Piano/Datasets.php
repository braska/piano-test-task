<?php

namespace Piano;

class Datasets {
    protected $datasets;

    public function __construct($datasets = [])
    {
        $this->datasets = $datasets;
    }


    public function add($file, $header, $records)
    {
        $this->datasets[] = ['file' => $file, 'header' => $header, 'records' => $records];
    }

    public function merge($merge_column_name)
    {
        $header = [$merge_column_name];
        $records = [];

        $record_index_by_merge_value = [];

        foreach ($this->datasets as $dataset) {
            $column_index_by_name = [];

            if (!in_array($merge_column_name, $dataset['header'])) {
                throw new \Exception("No \"" . $merge_column_name . "\" key presented in " . $dataset['file']);
            }

            foreach($dataset['header'] as $index => $column_name) {
                $column_index_by_name[$column_name] = $index;

                if ($column_name !== $merge_column_name) {
                    $header[] = $column_name;
                }
            }

            foreach($dataset['records'] as $record) {
                $merge_column_index = $column_index_by_name[$merge_column_name];

                $merge_value = $record[$merge_column_index];
                if (array_key_exists($merge_value, $record_index_by_merge_value)) {

                 } else {
                    $records[] = [$merge_value, array_filter($record, function($index) {
                        if ($merge_column_index !== $index) {
                            return true;
                        } else {
                            return false;
                        }
                    }, ARRAY_FILTER_USE_KEY)];
                    $record_index_by_merge_value[$merge_value] = count($records) - 1;
                }
            }
        }

        return ['header' => $header, 'records' => []];
    }
}
