<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service for handling predefined criteria with automatic scoring rules
 * for the SAW (Simple Additive Weighting) decision support system.
 */
class PredefinedCriteriaService
{
    /**
     * Predefined criteria definitions with their scoring rules
     */
    private const PREDEFINED_CRITERIA = [
        'class_attendance_percentage' => [
            'name' => 'Rata-Rata Kehadiran Siswa di Kelas',
            'type' => 'benefit',
            'data_type' => 'numeric',
            'unit' => '%',
            'description' => 'Persentase kehadiran siswa di kelas (dalam angka 0-100)',
            'value_scale' => ['min' => 0, 'max' => 100], // Indicates raw measurement 0-100
            'scoring_rules' => [
                ['min' => 0, 'max' => 50, 'score' => 1],
                ['min' => 50.01, 'max' => 60, 'score' => 2],
                ['min' => 60.01, 'max' => 70, 'score' => 3],
                ['min' => 70.01, 'max' => 80, 'score' => 4],
                ['min' => 80.01, 'max' => 100, 'score' => 5],
            ]
        ],
        'average_score' => [
            'name' => 'Nilai Rata-Rata Siswa',
            'type' => 'benefit',
            'data_type' => 'numeric',
            'unit' => '',
            'description' => 'Nilai rata-rata akademik siswa (dalam angka 0-100)',
            'value_scale' => ['min' => 0, 'max' => 100], // Indicates raw measurement 0-100
            'scoring_rules' => [
                ['min' => 0, 'max' => 50, 'score' => 1],
                ['min' => 50.01, 'max' => 60, 'score' => 2],
                ['min' => 60.01, 'max' => 70, 'score' => 3],
                ['min' => 70.01, 'max' => 80, 'score' => 4],
                ['min' => 80.01, 'max' => 100, 'score' => 5],
            ]
        ],
        'major_relevance' => [
            'name' => 'Kesesuaian Jurusan yang Dipilih Dengan Jurusan Saat Ini',
            'type' => 'benefit',
            'data_type' => 'qualitative_option',
            'description' => 'Relevansi jurusan yang dipilih dengan jurusan saat ini',
            'value_scale' => ['min' => 1, 'max' => 5], // Indicates 1-5 scaled score from options
            'options' => [
                ['label' => 'Tidak ada hubungan', 'value' => 'no_relation', 'numeric_value' => 1],
                ['label' => 'Berbeda bidang tetapi masih dalam kelompok yang sama', 'value' => 'same_group', 'numeric_value' => 2],
                ['label' => 'Berbeda tetapi terdapat keterkaitan di beberapa mata kuliah', 'value' => 'some_connection', 'numeric_value' => 3],
                ['label' => 'Hampir sama hanya berbeda fokus bidang', 'value' => 'similar_focus', 'numeric_value' => 4],
                ['label' => 'Sangat relevan dengan jurusan saat ini', 'value' => 'highly_relevant', 'numeric_value' => 5],
            ]
        ],
        'extracurricular_activeness' => [
            'name' => 'Keaktifan di Ekstrakurikuler/Organisasi',
            'type' => 'benefit',
            'data_type' => 'numeric', // Student input is 1-5, getRawValueForPredefinedCriterion scales it to 0-100
            'unit' => '%',
            'description' => 'Persentase keaktifan dalam kegiatan ekstrakurikuler atau organisasi (skor 0-100, derived from 1-5 input)',
            'value_scale' => ['min' => 0, 'max' => 100], // Reflects the 0-100 output of getRawValueForPredefinedCriterion
            // Scoring rules here are for the 0-100 scale that SAWCalculatorService will receive
            'scoring_rules' => [
                ['min' => 0, 'max' => 50.00, 'score' => 1], // e.g. 1-2 from student (0-40%)
                ['min' => 50.01, 'max' => 60.00, 'score' => 2], // e.g. 3 from student (60%)
                ['min' => 60.01, 'max' => 70.00, 'score' => 3], // e.g. 4 from student (80%)
                ['min' => 70.01, 'max' => 80.00, 'score' => 4], // e.g. 5 from student (100%)
                ['min' => 80.01, 'max' => 100, 'score' => 5], // e.g. 5 from student (100%)
            ]
        ],
        'graduation_time_gap' => [
            'name' => 'Jarak Pendaftaran Dengan Tahun Lulus',
            'type' => 'cost',
            'data_type' => 'numeric',
            'unit' => 'bulan',
            'description' => 'Jarak waktu antara pendaftaran dengan tahun lulus (dalam bulan), scored 1-5.',
            'value_scale' => ['min' => 1, 'max' => 5], // Indicates 1-5 scaled score from scoring_rules
            'scoring_rules' => [
                ['min_months' => 25, 'max_months' => 999, 'score_value' => 5], // Higher gap, higher cost score (bad)
                ['min_months' => 19, 'max_months' => 24, 'score_value' => 4],
                ['min_months' => 13, 'max_months' => 17, 'score_value' => 3],
                ['min_months' => 7, 'max_months' => 12, 'score_value' => 2],
                ['min_months' => 0, 'max_months' => 6, 'score_value' => 1], // Lower gap, lower cost score (good)
            ]
        ],
        'tuition_payment_delays' => [
            'name' => 'Penunggakan Pembayaran SPP',
            'type' => 'cost',
            'data_type' => 'qualitative_option',
            'description' => 'Status keterlambatan pembayaran SPP, scored 1-5.',
            'value_scale' => ['min' => 1, 'max' => 5], // Indicates 1-5 scaled score from options
            'options' => [ // Higher numeric_value means higher cost (worse)
                ['label' => 'Tepat waktu', 'value' => 'on_time', 'numeric_value' => 1], // Best case for cost
                ['label' => 'Terlambat < 1 bulan', 'value' => 'late_under_1_month', 'numeric_value' => 2],
                ['label' => 'Menunggak selama 1 bulan', 'value' => 'arrears_1_month', 'numeric_value' => 3],
                ['label' => 'Menunggak selama 2 bulan', 'value' => 'arrears_2_months', 'numeric_value' => 4],
                ['label' => 'Menunggak > 3 bulan', 'value' => 'arrears_over_3_months', 'numeric_value' => 5], // Worst case for cost
            ]
        ],
        'disciplinary_warnings' => [
            'name' => 'Sanksi Surat Peringatan',
            'type' => 'cost',
            'data_type' => 'qualitative_option',
            'description' => 'Status surat peringatan atau sanksi disiplin, scored 1-5.',
            'value_scale' => ['min' => 1, 'max' => 5], // Indicates 1-5 scaled score from options
            'options' => [ // Higher numeric_value means higher cost (worse)
                ['label' => 'Tidak pernah melanggar', 'value' => 'no_violation', 'numeric_value' => 1], // Best case
                ['label' => 'Pemanggilan orang tua/wali untuk pembinaan, SP1', 'value' => 'parent_call', 'numeric_value' => 2],
                ['label' => 'Pemanggilan orang tua dengan pernyataan bermaterai dan skorsing 3 hari', 'value' => 'suspension_3_days', 'numeric_value' => 3],
                ['label' => 'Pemanggilan orang tua, Surat Peringatan II, dan skorsing 6 hari', 'value' => 'suspension_6_days', 'numeric_value' => 4],
                ['label' => 'Dikembalikan ke orang tua/wali (pelanggaran paling berat)', 'value' => 'expelled', 'numeric_value' => 5], // Worst case
            ]
        ]
    ];

    /**
     * Get all available predefined criteria names for dropdown selection
     */
    public function getAvailableCriteriaNames(): array
    {
        $names = [];
        foreach (self::PREDEFINED_CRITERIA as $key => $criteria) {
            $names[$key] = $criteria['name'];
        }
        return $names;
    }

    /**
     * Get predefined criteria configuration by key
     */
    public function getCriteriaDefinition(string $key): ?array
    {
        return self::PREDEFINED_CRITERIA[$key] ?? null;
    }

    /**
     * Check if a criterion key is predefined
     */
    public function isPredefinedCriteria(string $key): bool
    {
        return array_key_exists($key, self::PREDEFINED_CRITERIA);
    }

    /**
     * Check if a criterion is predefined
     *
     * @param string $criterionId
     * @return bool
     */
    public function isPredefinedCriterion(string $criterionId): bool
    {
        return isset(self::PREDEFINED_CRITERIA[$criterionId]);
    }

    /**
     * Get a structured configuration for a predefined criterion, suitable for batch storage.
     * Includes essential fields, value_scale, and options if applicable.
     *
     * @param string $key The key of the predefined criterion.
     * @param float $weight The weight assigned to this criterion in the batch.
     * @return array|null Null if the criterion key is invalid.
     */
    public function getPredefinedCriteriaConfig(string $key, float $weight): ?array
    {
        $definition = $this->getCriteriaDefinition($key);

        if (!$definition) {
            Log::warning("[PredefinedCriteriaService] Attempted to get config for unknown predefined criterion key: {$key}");
            return null;
        }

        $config = [
            'id' => $key,
            'name' => $definition['name'],
            'weight' => $weight,
            'type' => $definition['type'],
            'data_type' => $definition['data_type'],
            'is_predefined' => true, // Explicitly mark as predefined
            'unit' => $definition['unit'] ?? null,
            // 'description' => $definition['description'] ?? null, // Optional: can be added if needed
        ];

        if (isset($definition['value_scale'])) {
            $config['value_scale'] = $definition['value_scale'];
        }

        if ($definition['data_type'] === 'qualitative_option' && isset($definition['options'])) {
            $config['options'] = $definition['options'];
        }

        // The 'scoring_rules' from the main definition are primarily for the service's internal 'calculateScore' or
        // for specific transformations (like graduation_time_gap). They are generally not needed directly in criteria_config
        // if SAW is using value_scale for normalization of the processed value.

        return $config;
    }

    /**
     * Apply automatic scoring to a raw input value based on predefined rules
     */
    public function applyAutomaticScoring(string $criteriaKey, $rawValue): ?float
    {
        $definition = $this->getCriteriaDefinition($criteriaKey);

        if (!$definition) {
            Log::warning("[PredefinedCriteriaService] Unknown criteria key: {$criteriaKey}");
            return null;
        }

        // For numeric criteria with scoring rules
        if ($definition['data_type'] === 'numeric' && isset($definition['scoring_rules'])) {
            return $this->applyNumericScoring($definition['scoring_rules'], (float)$rawValue);
        }

        // For qualitative criteria with options
        if ($definition['data_type'] === 'qualitative_option' && isset($definition['options'])) {
            return $this->applyQualitativeScoring($definition['options'], $rawValue);
        }

        Log::warning("[PredefinedCriteriaService] No scoring rules found for criteria: {$criteriaKey}");
        return null;
    }

    /**
     * Calculate score for a predefined criterion based on raw value
     *
     * @param string $criterionId
     * @param mixed $rawValue
     * @return float
     */
    public function calculateScore(string $criterionId, $rawValue): float
    {
        if (!$this->isPredefinedCriterion($criterionId)) {
            Log::warning("[PredefinedCriteriaService->calculateScore] Unknown predefined criterion: {$criterionId}");
            return 0.0;
        }

        $criteria = self::PREDEFINED_CRITERIA[$criterionId];

        if ($criteria['data_type'] === 'numeric') {
            return $this->calculateNumericScore($criteria['scoring_rules'], (float)$rawValue);
        } elseif ($criteria['data_type'] === 'qualitative_option') {
            return $this->calculateQualitativeScore($criteria['options'], $rawValue);
        }

        Log::warning("[PredefinedCriteriaService->calculateScore] Unsupported data type for criterion: {$criterionId}");
        return 0.0;
    }

    /**
     * Apply numeric scoring rules to a raw value
     */
    private function applyNumericScoring(array $scoringRules, float $rawValue): float
    {
        foreach ($scoringRules as $rule) {
            $min = $rule['min'];
            $max = $rule['max'];

            // Check if the raw value falls within this rule's range
            if ($rawValue >= $min && $rawValue <= $max) {
                Log::debug("[PredefinedCriteriaService] Applied scoring rule for value {$rawValue}: score {$rule['score']}");
                return (float)$rule['score'];
            }
        }

        // If no rule matches, return the lowest score (1)
        Log::warning("[PredefinedCriteriaService] No scoring rule matched for value {$rawValue}, returning default score 1");
        return 1.0;
    }

    /**
     * Calculate score for numeric criteria based on scoring rules
     *
     * @param array $scoringRules
     * @param float $value
     * @return float
     */
    private function calculateNumericScore(array $scoringRules, float $value): float
    {
        foreach ($scoringRules as $rule) {
            if ($value >= $rule['min'] && $value <= $rule['max']) {
                return (float)$rule['score'];
            }
        }

        // Default to lowest score if value doesn't fit any range
        return 1.0;
    }

    /**
     * Apply qualitative scoring to a raw value
     */
    private function applyQualitativeScoring(array $options, $rawValue): float
    {
        foreach ($options as $option) {
            if ($option['value'] === $rawValue) {
                Log::debug("[PredefinedCriteriaService] Applied qualitative scoring for value {$rawValue}: score {$option['numeric_value']}");
                return (float)$option['numeric_value'];
            }
        }

        // If no option matches, return the lowest score (1)
        Log::warning("[PredefinedCriteriaService] No qualitative option matched for value {$rawValue}, returning default score 1");
        return 1.0;
    }

    /**
     * Calculate score for qualitative criteria based on options
     *
     * @param array $options
     * @param mixed $value
     * @return float
     */
    private function calculateQualitativeScore(array $options, $value): float
    {
        foreach ($options as $option) {
            if ($option['value'] === $value) {
                return (float)$option['numeric_value'];
            }
        }

        // Default to lowest score if value doesn't match any option
        return 1.0;
    }

    /**
     * Transform raw submission values to scored values for predefined criteria
     */
    public function transformSubmissionValues(array $rawCriteriaValues, array $batchCriteriaConfig): array
    {
        $transformedValues = [];

        foreach ($batchCriteriaConfig as $criterion) {
            $criterionId = $criterion['id'];

            // Skip if raw value doesn't exist
            if (!array_key_exists($criterionId, $rawCriteriaValues)) {
                continue;
            }

            $rawValue = $rawCriteriaValues[$criterionId];

            // Check if this is a predefined criterion
            if (isset($criterion['predefined']) && $criterion['predefined'] === true) {
                $scoredValue = $this->applyAutomaticScoring($criterionId, $rawValue);
                if ($scoredValue !== null) {
                    $transformedValues[$criterionId] = $scoredValue;
                    Log::debug("[PredefinedCriteriaService] Transformed {$criterionId}: {$rawValue} → {$scoredValue}");
                } else {
                    // If scoring fails, keep original value
                    $transformedValues[$criterionId] = $rawValue;
                }
            } else {
                // For non-predefined criteria, handle existing value_map logic
                if (isset($criterion['value_map']) && is_array($criterion['value_map'])) {
                    $transformedValues[$criterionId] = $criterion['value_map'][$rawValue] ?? $rawValue;
                } else {
                    $transformedValues[$criterionId] = $rawValue;
                }
            }
        }

        return $transformedValues;
    }

    /**
     * Validate that predefined criteria data meets the required format
     */
    public function validatePredefinedCriteria(): array
    {
        $errors = [];

        foreach (self::PREDEFINED_CRITERIA as $key => $definition) {
            // Required fields validation
            $requiredFields = ['name', 'type', 'data_type'];
            foreach ($requiredFields as $field) {
                if (!isset($definition[$field])) {
                    $errors[] = "Criteria '{$key}' missing required field: {$field}";
                }
            }

            // Type validation
            if (isset($definition['type']) && !in_array($definition['type'], ['benefit', 'cost'])) {
                $errors[] = "Criteria '{$key}' has invalid type: {$definition['type']}";
            }

            // Data type validation
            if (isset($definition['data_type']) && !in_array($definition['data_type'], ['numeric', 'qualitative_option'])) {
                $errors[] = "Criteria '{$key}' has invalid data_type: {$definition['data_type']}";
            }

            // Numeric criteria should have scoring rules
            if ($definition['data_type'] === 'numeric' && !isset($definition['scoring_rules'])) {
                $errors[] = "Numeric criteria '{$key}' missing scoring_rules";
            }

            // Qualitative criteria should have options
            if ($definition['data_type'] === 'qualitative_option' && !isset($definition['options'])) {
                $errors[] = "Qualitative criteria '{$key}' missing options";
            }

            // Validate scoring rules format
            if (isset($definition['scoring_rules'])) {
                foreach ($definition['scoring_rules'] as $index => $rule) {
                    if (!isset($rule['min'], $rule['max'], $rule['score'])) {
                        $errors[] = "Criteria '{$key}' scoring rule {$index} missing required fields";
                    }
                }
            }

            // Validate options format
            if (isset($definition['options'])) {
                foreach ($definition['options'] as $index => $option) {
                    if (!isset($option['label'], $option['value'], $option['numeric_value'])) {
                        $errors[] = "Criteria '{$key}' option {$index} missing required fields";
                    }
                }
            }
        }

        return $errors;
    }
}
