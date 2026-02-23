<?php

namespace Core;

/**
 * Validator
 * 
 * Simple validation class for form inputs.
 * 
 * Usage:
 *   $validator = new Validator($_POST, [
 *       'email' => 'required|email|max:255',
 *       'name' => 'required|min:3|max:100',
 *       'age' => 'numeric|min:18|max:120'
 *   ]);
 * 
 *   if ($validator->fails()) {
 *       $errors = $validator->errors();
 *   }
 * 
 * @package Core
 */
class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $messages = [];
    
    /**
     * Custom error messages
     */
    private array $defaultMessages = [
        'required' => 'The :field field is required.',
        'email' => 'The :field must be a valid email address.',
        'min' => 'The :field must be at least :param characters.',
        'max' => 'The :field must not exceed :param characters.',
        'numeric' => 'The :field must be a number.',
        'integer' => 'The :field must be an integer.',
        'url' => 'The :field must be a valid URL.',
        'alpha' => 'The :field may only contain letters.',
        'alphanumeric' => 'The :field may only contain letters and numbers.',
        'in' => 'The selected :field is invalid.',
        'confirmed' => 'The :field confirmation does not match.',
        'same' => 'The :field and :param must match.',
        'different' => 'The :field and :param must be different.',
        'unique' => 'The :field has already been taken.',
    ];
    
    /**
     * Constructor
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @param array $messages Custom error messages (optional)
     */
    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = array_merge($this->defaultMessages, $messages);
        
        $this->validate();
    }
    
    /**
     * Run validation
     * 
     * @return void
     */
    private function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            
            foreach ($rules as $rule) {
                $this->validateRule($field, $rule);
            }
        }
    }
    
    /**
     * Validate a single rule
     * 
     * @param string $field Field name
     * @param string $rule Rule with optional parameter (e.g., 'min:3')
     * @return void
     */
    private function validateRule(string $field, string $rule): void
    {
        // Parse rule and parameter
        $parts = explode(':', $rule, 2);
        $ruleName = $parts[0];
        $parameter = $parts[1] ?? null;
        
        $value = $this->data[$field] ?? null;
        
        // Call validation method
        $method = 'validate' . ucfirst($ruleName);
        
        if (method_exists($this, $method)) {
            $passes = $this->$method($value, $parameter, $field);
            
            if (!$passes) {
                $this->addError($field, $ruleName, $parameter);
            }
        }
    }
    
    /**
     * Add an error message
     * 
     * @param string $field Field name
     * @param string $rule Rule name
     * @param string|null $parameter Rule parameter
     * @return void
     */
    private function addError(string $field, string $rule, ?string $parameter): void
    {
        $message = $this->messages[$rule] ?? "The $field field is invalid.";
        $message = str_replace(':field', $field, $message);
        $message = str_replace(':param', (string)$parameter, $message);
        
        $this->errors[$field][] = $message;
    }
    
    /**
     * Check if validation failed
     * 
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Check if validation passed
     * 
     * @return bool
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }
    
    /**
     * Get all errors
     * 
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get errors for a specific field
     * 
     * @param string $field Field name
     * @return array
     */
    public function getErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }
    
    /**
     * Get first error for a field
     * 
     * @param string $field Field name
     * @return string|null
     */
    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
    
    // ============ VALIDATION RULES ============
    
    /**
     * Required validation
     */
    private function validateRequired($value): bool
    {
        if (is_null($value)) {
            return false;
        }
        
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        
        if (is_array($value) && empty($value)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Email validation
     */
    private function validateEmail($value): bool
    {
        if (empty($value)) {
            return true; // Use 'required' rule for required fields
        }
        
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Minimum length validation
     */
    private function validateMin($value, $min): bool
    {
        if (empty($value)) {
            return true;
        }
        
        if (is_numeric($value)) {
            return $value >= $min;
        }
        
        return mb_strlen($value) >= $min;
    }
    
    /**
     * Maximum length validation
     */
    private function validateMax($value, $max): bool
    {
        if (empty($value)) {
            return true;
        }
        
        if (is_numeric($value)) {
            return $value <= $max;
        }
        
        return mb_strlen($value) <= $max;
    }
    
    /**
     * Numeric validation
     */
    private function validateNumeric($value): bool
    {
        if (empty($value)) {
            return true;
        }
        
        return is_numeric($value);
    }
    
    /**
     * Integer validation
     */
    private function validateInteger($value): bool
    {
        if (empty($value)) {
            return true;
        }
        
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
    
    /**
     * URL validation
     */
    private function validateUrl($value): bool
    {
        if (empty($value)) {
            return true;
        }
        
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Alpha validation (letters only)
     */
    private function validateAlpha($value): bool
    {
        if (empty($value)) {
            return true;
        }
        
        return preg_match('/^[a-zA-Z]+$/', $value) === 1;
    }
    
    /**
     * Alphanumeric validation
     */
    private function validateAlphanumeric($value): bool
    {
        if (empty($value)) {
            return true;
        }
        
        return preg_match('/^[a-zA-Z0-9]+$/', $value) === 1;
    }
    
    /**
     * In array validation
     */
    private function validateIn($value, $list): bool
    {
        if (empty($value)) {
            return true;
        }
        
        $options = explode(',', $list);
        return in_array($value, $options, true);
    }
    
    /**
     * Confirmation validation (_confirmation field must match)
     */
    private function validateConfirmed($value, $param, $field): bool
    {
        if (empty($value)) {
            return true;
        }
        
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;
        
        return $value === $confirmValue;
    }
    
    /**
     * Same validation (must match another field)
     */
    private function validateSame($value, $otherField): bool
    {
        if (empty($value)) {
            return true;
        }
        
        $otherValue = $this->data[$otherField] ?? null;
        return $value === $otherValue;
    }
    
    /**
     * Different validation (must differ from another field)
     */
    private function validateDifferent($value, $otherField): bool
    {
        if (empty($value)) {
            return true;
        }
        
        $otherValue = $this->data[$otherField] ?? null;
        return $value !== $otherValue;
    }
    
    /**
     * Unique validation (check database)
     * Format: unique:table,column
     */
    private function validateUnique($value, $params): bool
    {
        if (empty($value)) {
            return true;
        }
        
        $parts = explode(',', $params);
        $table = $parts[0] ?? null;
        $column = $parts[1] ?? 'email';
        $ignoreId = $parts[2] ?? null;  // Optional: ID to ignore (for updates)
        
        if (!$table) {
            return true;
        }
        
        try {
            $db = \Core\Database::getInstance();
            
            if ($ignoreId !== null) {
                // When updating, ignore the current record
                $result = $db->fetch(
                    "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ? AND id != ?",
                    [$value, $ignoreId]
                );
            } else {
                // For new records, just check if exists
                $result = $db->fetch(
                    "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?",
                    [$value]
                );
            }
            
            return ($result['count'] ?? 0) === 0;
        } catch (\Exception $e) {
            // If database check fails, allow (don't block user)
            error_log("Unique validation failed: " . $e->getMessage());
            return true;
        }
    }
}
