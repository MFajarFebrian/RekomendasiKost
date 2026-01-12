<?php
/**
 * Validator Utility Class
 * Input validation and sanitization
 */

class Validator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * Set data to validate
     */
    public function setData($data) {
        $this->data = $data;
        $this->errors = [];
        return $this;
    }
    
    /**
     * Check if field is required
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? ucfirst($field) . ' is required';
        }
        return $this;
    }
    
    /**
     * Validate email format
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? 'Invalid email format';
        }
        return $this;
    }
    
    /**
     * Validate minimum length
     */
    public function minLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at least $length characters";
        }
        return $this;
    }
    
    /**
     * Validate numeric value
     */
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = $message ?? ucfirst($field) . ' must be a number';
        }
        return $this;
    }
    
    /**
     * Validate number is greater than value
     */
    public function min($field, $value, $message = null) {
        if (isset($this->data[$field]) && $this->data[$field] < $value) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at least $value";
        }
        return $this;
    }
    
    /**
     * Validate number is less than value
     */
    public function max($field, $value, $message = null) {
        if (isset($this->data[$field]) && $this->data[$field] > $value) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at most $value";
        }
        return $this;
    }
    
    /**
     * Validate number is between min and max
     */
    public function between($field, $min, $max, $message = null) {
        if (isset($this->data[$field])) {
            $value = $this->data[$field];
            if ($value < $min || $value > $max) {
                $this->errors[$field] = $message ?? ucfirst($field) . " must be between $min and $max";
            }
        }
        return $this;
    }
    
    /**
     * Check if validation passed
     */
    public function isValid() {
        return empty($this->errors);
    }
    
    /**
     * Get validation errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get sanitized value
     */
    public function get($field, $default = null) {
        return isset($this->data[$field]) ? trim($this->data[$field]) : $default;
    }
    
    /**
     * Get all sanitized data
     */
    public function getData() {
        return array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $this->data);
    }
    
    /**
     * Static helper: sanitize string
     */
    public static function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Static helper: sanitize for output
     */
    public static function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
