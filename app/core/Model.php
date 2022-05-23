<?php

namespace app\core;

abstract class Model
{

    const RULE_REQUIRED = 'required';
    const RULE_EMAIL = 'email';
    const RULE_FILE = 'file';
    const RULE_MATCH = 'match';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_UNIQUE = 'unique';


    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public array $errors = [];

    abstract public function rules(): array;

    public function validate()
    {
        foreach ($this->rules() as $attr => $rules) {
            $value = $this->{$attr} ?? '';
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && (empty($value) || str_replace(' ', '', $value) === '')) {
                    $this->addError($attr, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attr, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && mb_strlen($value) < $rule['min']) {
                    $this->addError($attr, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && mb_strlen($value) > $rule['max']) {
                    
                    $this->addError($attr, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addError($attr, self::RULE_MATCH);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attr'] ?? $attr;
                    $className = new $className();
                    $tableName = $className->table();
                    $statement = Application::$app->db->prepare("select * from $tableName where $uniqueAttr = :attr ;");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $result = $statement->fetchObject();
                    if ($result) {
                        $this->addError($attr, self::RULE_UNIQUE, ['field' => $this->{$attr}]);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function addError(string $attribute, string $rule, $params = [])
    {

        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        // $this->errors[$attribute][] = $message;
        $this->errors[] = $message;
    }

    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED => '您有必填欄位尚未填寫，請確認',
            self::RULE_EMAIL => '請輸入電子郵件',
            self::RULE_MATCH => '此欄位必須與 {match} 一致',
            self::RULE_MIN => '此欄位不可小於 {min} 字元',
            self::RULE_MAX => '此欄位不可超過 {max} 字元',
            self::RULE_UNIQUE => '{field} 已被使用',
        ];
    }

    public function hasError($attribute = null)
    {
        return $this->errors ?? false;
    }

    public function getAttrFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }

    public function getFirstError()
    {
        return $this->errors[0] ?? false;
    }
}
