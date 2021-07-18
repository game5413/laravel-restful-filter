<?php

namespace Kemodev\RestfulFilter;

use InvalidArgumentException;
use PhpParser\Node\Expr\Instanceof_;

trait Filterable {
    protected $searchOperations = [
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'like' => 'LIKE',
        'not' => '!='
    ];

    /**
     * 
     * @param \Illuminate\Database\Eloquent\Builder $instance
     * @param array $column
     * @param string $logical_operator
     * @param string $value
     * @param string $table_column
     * @param integer $index
     * @return isRelationSearch|\Illuminate\Database\Eloquent\Builder
     */
    protected function isRelationSearch($instance, $column, $logical_operator, $value, $table_column, $index) {
        if (!count($column)) {
            return $instance->where($table_column, $logical_operator, $value);
        }
        $relation = $column[0];
        array_shift($column);
        return $instance->whereHas($relation, function($query) use($column, $logical_operator, $value, $table_column, $index) {
            return $this->isRelationSearch($query, $column, $logical_operator, $value, $table_column, $index);
        });
    }

    /**
     * 
     * @param \Illuminate\Database\Eloquent\Builder $instance
     * @param string $column
     * @param string $logical_operator
     * @param string $value
     * @return void
     */
    protected function searchType($instance, $column, $logical_operator, $value) {
        $table_columns = explode(',', $column);
        foreach ($table_columns as $index => $tbl_column) {
            $tbl_column = explode('.', $tbl_column);
            $had_relation = count($tbl_column);
            if ($had_relation > 1) {
                $column = $tbl_column[$had_relation - 1];
                $relation = $tbl_column[0];
                array_pop($tbl_column);
                array_shift($tbl_column);
                $instance->{!$index ? 'whereHas' : 'orWhereHas'}($relation, function($query) use($tbl_column, $column, $logical_operator, $value, $index) {
                    return $this->isRelationSearch($query, $tbl_column, $logical_operator, $value, $column, $index);
                });
                continue;
            }
            $is_date = in_array(strtolower($tbl_column[0]), array_map('strtolower', $this->dates));
            if ($is_date) {
                $instance->whereDate($tbl_column[0], $logical_operator, $value);
                return;
            }
            if ($index) {
                $instance->orWhere($tbl_column[0], $logical_operator, $value);
                continue;
            }
            $instance->where($tbl_column[0], $logical_operator, $value);
        }
    }

    /**
     * 
     * @param \Illuminate\Database\Eloquent\Builder $instance
     * @param null|array $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function searchableColumn($instance, $columns) {
        if (!$columns) {
            return $instance;
        }
        if (!is_array($columns)) {
            throw new InvalidArgumentException(
                'Invalid filter variables, expecting array got '.gettype($columns)
            );
        }
        if (!$this->filterableColumns) {
            $this->filterableColumns = [];
        }
        foreach ($this->filterableColumns as $column_key => $column) {
            if (array_key_exists($column_key, $columns)) {
                $query = explode(':', $columns[$column_key]);
                if (count($query) == 1) {
                    $this->searchType($instance, $column, '=', $columns[$column_key]);
                    continue;
                }
                if ($query[0] === 'like') {
                    $this->searchType($instance, $column, $this->searchOperations[$query[0]], "%".$query[1]."%");
                    continue;
                }
                $this->searchType($instance, $column, $this->searchOperations[$query[0]], $query[1]);
                continue;
            }
        }
        return $instance;
    }

    /**
     * 
     * @TODO sorting through relation
     * 
     * @param \Illuminate\Database\Eloquent\Builder $instance
     * @param null|string $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function sortableColumn($instance, $columns) {
        if (!$columns) {
            return $instance->orderBy($this->primaryKey, 'asc');
        }
        if (!is_string($columns)) {
            throw new InvalidArgumentException(
                'Invalid sorting variables, expecting string got '.gettype($columns)
            );
        }
        if (!$this->sortableColumns) {
            $this->sortableColumns = [];
        }
        $columns = explode(',', $columns);
        foreach ($columns as $column) {
            $key = explode('_', $column);
            $direction = array_pop($key);
            $key = implode('_', $key);
            if (array_key_exists($key, $this->sortableColumns)) {
                $relation = explode('.', $this->sortableColumns[$key]);
                if (count($relation) > 1) {
                    $instance->with([$relation[0] => function($query) use ($direction, $relation) {
                        $query->orderBy($relation[1], strtoupper($direction));
                    }]);
                } else {
                    $instance->orderBy($this->sortableColumns[$key], strtoupper($direction));
                }
            }
        }
        return $instance;
    }

    public function scopeSearchable($instance, $columns = []) {
        return $this->searchableColumn($instance, $columns);
    }

    public function scopeSortable($instance, $columns = '') {
        return $this->sortableColumn($instance, $columns);
    }
}
