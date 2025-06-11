<?php

namespace App\Livewire\Admin\ScholarshipBatches;

use App\Models\ScholarshipBatch;
use App\Services\PredefinedCriteriaService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
class EditScholarshipBatch extends Component
{
    public ScholarshipBatch $batch;
    public int $batchId;

    public string $name = '';
    public string $description = '';
    public string $start_date = '';
    public string $end_date = '';
    public array $criteria = [];

    public array $availableCriteriaNames = [];

    protected PredefinedCriteriaService $predefinedCriteriaService;

    public function boot(PredefinedCriteriaService $predefinedCriteriaService)
    {
        $this->predefinedCriteriaService = $predefinedCriteriaService;
    }

    public function mount(ScholarshipBatch $batch)
    {
        // Load predefined criteria names
        $this->availableCriteriaNames = $this->predefinedCriteriaService->getAvailableCriteriaNames();

        $this->batch = $batch;
        $this->batchId = $batch->id;
        $this->name = $batch->name;
        $this->description = $batch->description ?? '';
        $this->start_date = $batch->start_date->format('Y-m-d');
        $this->end_date = $batch->end_date->format('Y-m-d');

        if (is_array($batch->criteria_config)) {
            foreach ($batch->criteria_config as $dbCriterion) {
                $criterionFormEntry = [
                    'component_id' => 'crit_' . Str::random(8),
                    'db_id' => $dbCriterion['id'] ?? null, // Store original ID from DB
                    'name_key' => '',
                    'custom_name_input' => '',
                    'display_name' => $dbCriterion['name'] ?? 'Criterion',
                    'weight' => $dbCriterion['weight'] ?? null,
                    'type' => $dbCriterion['type'] ?? 'benefit',
                    'data_type' => $dbCriterion['data_type'] ?? 'numeric',
                    'options_config_type' => 'none',
                    'options' => [],
                    'value_map' => [],
                    'is_predefined' => false,
                    // 'value_scale' will be populated below if predefined
                ];

                // Determine if it was a predefined name or custom
                if (isset($this->availableCriteriaNames[$dbCriterion['id']])) {
                    $criterionFormEntry['name_key'] = $dbCriterion['id'];
                    $criterionFormEntry['is_predefined'] = true;
                    // Fetch value_scale for predefined criteria
                    $predefinedConfig = $this->predefinedCriteriaService->getCriteriaDefinition($dbCriterion['id']);
                    if ($predefinedConfig && isset($predefinedConfig['value_scale'])) {
                        $criterionFormEntry['value_scale'] = $predefinedConfig['value_scale'];
                    }
                } else {
                    $criterionFormEntry['custom_name_input'] = $dbCriterion['name'] ?? '';
                    // Ensure value_scale is not set for custom criteria from DB if it somehow exists
                    unset($criterionFormEntry['value_scale']);
                }

                if ($criterionFormEntry['data_type'] === 'qualitative_option' && isset($dbCriterion['options']) && is_array($dbCriterion['options'])) {
                    $criterionFormEntry['options_config_type'] = 'options';
                    foreach ($dbCriterion['options'] as $opt) {
                        $criterionFormEntry['options'][] = [
                            'label' => $opt['label'] ?? '',
                            'value' => $opt['value'] ?? '',
                            'numeric_value' => $opt['numeric_value'] ?? null,
                        ];
                    }
                }

                if ($criterionFormEntry['data_type'] === 'qualitative_text' && isset($dbCriterion['value_map']) && is_array($dbCriterion['value_map'])) {
                    $criterionFormEntry['options_config_type'] = 'value_map';
                    foreach ($dbCriterion['value_map'] as $key => $val) {
                        $criterionFormEntry['value_map'][] = ['key_input' => $key, 'value_input' => $val];
                    }
                }
                $this->criteria[] = $criterionFormEntry;
            }
        }

        if (empty($this->criteria)) {
            $this->addCriterion();
        }
    }

    public function addCriterion(): void
    {
        $this->criteria[] = [
            'component_id' => 'crit_' . Str::random(8),
            'db_id' => null, // New criteria don't have a db_id yet
            'name_key' => '',
            'custom_name_input' => '',
            'display_name' => 'New Criterion',
            'weight' => null,
            'type' => 'benefit',
            'data_type' => 'numeric',
            'options_config_type' => 'none',
            'options' => [],
            'value_map' => [],
            'is_predefined' => false,
        ];
    }

    public function removeCriterion(int $index): void
    {
        unset($this->criteria[$index]);
        $this->criteria = array_values($this->criteria);
    }

    public function addOption(int $criterionIndex): void
    {
        $this->criteria[$criterionIndex]['options'][] = ['label' => '', 'value' => '', 'numeric_value' => null];
    }

    public function removeOption(int $criterionIndex, int $optionIndex): void
    {
        unset($this->criteria[$criterionIndex]['options'][$optionIndex]);
        $this->criteria[$criterionIndex]['options'] = array_values($this->criteria[$criterionIndex]['options']);
    }

    public function addValueMapEntry(int $criterionIndex): void
    {
        $this->criteria[$criterionIndex]['value_map'][] = ['key_input' => '', 'value_input' => null];
    }

    public function removeValueMapEntry(int $criterionIndex, int $valueMapIndex): void
    {
        unset($this->criteria[$criterionIndex]['value_map'][$valueMapIndex]);
        $this->criteria[$criterionIndex]['value_map'] = array_values($this->criteria[$criterionIndex]['value_map']);
    }

    public function updatedCriteria($value, $key): void
    {
        $parts = explode('.', $key);
        $index = (int)$parts[0];
        $field = $parts[1] ?? null;

        if (!isset($this->criteria[$index])) {
            return;
        }

        $criterion = &$this->criteria[$index];

        if ($field === 'name_key') {
            if (!empty($criterion['name_key'])) {
                $criterion['custom_name_input'] = '';
                $criterion['display_name'] = $this->availableCriteriaNames[$criterion['name_key']] ?? 'Unknown Criterion';

                // Auto-populate predefined criteria configuration
                $predefinedConfig = $this->predefinedCriteriaService->getCriteriaDefinition($criterion['name_key']);
                if ($predefinedConfig) {
                    $criterion['type'] = $predefinedConfig['type'];
                    $criterion['data_type'] = $predefinedConfig['data_type'];
                    $criterion['is_predefined'] = true;

                    // Store value_scale if present for predefined
                    if (isset($predefinedConfig['value_scale'])) {
                        $criterion['value_scale'] = $predefinedConfig['value_scale'];
                    } else {
                        unset($criterion['value_scale']); // Remove if not in definition
                    }

                    // Set up options for qualitative criteria
                    if ($predefinedConfig['data_type'] === 'qualitative_option' && isset($predefinedConfig['options'])) {
                        $criterion['options_config_type'] = 'options';
                        $criterion['options'] = $predefinedConfig['options'];
                    } else {
                        $criterion['options_config_type'] = 'none';
                        $criterion['options'] = [];
                    }
                }
            } elseif (empty($criterion['custom_name_input'])) {
                $criterion['display_name'] = 'New Criterion';
                $criterion['is_predefined'] = false;
                unset($criterion['value_scale']); // Remove for new/empty criterion
            }
        } elseif ($field === 'custom_name_input') {
            if (!empty($criterion['custom_name_input'])) {
                $criterion['name_key'] = '';
                $criterion['display_name'] = $criterion['custom_name_input'];
                $criterion['is_predefined'] = false;
                unset($criterion['value_scale']); // Remove for custom criterion
                // Reset to defaults for custom criteria
                $criterion['options_config_type'] = 'none';
                $criterion['options'] = [];
            } elseif (empty($criterion['name_key'])) {
                $criterion['display_name'] = 'New Criterion';
            }
        } elseif ($field === 'data_type') {
            $criterion['options'] = [];
            $criterion['value_map'] = [];
            if ($criterion['data_type'] === 'qualitative_option') {
                $criterion['options_config_type'] = 'options';
                $this->addOption($index);
            } elseif ($criterion['data_type'] === 'qualitative_text') {
                $criterion['options_config_type'] = 'value_map';
                $this->addValueMapEntry($index);
            } else {
                $criterion['options_config_type'] = 'none';
            }
        }
        unset($criterion);
    }

    // updateDisplayName is effectively handled by updatedCriteria now.

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'criteria' => 'required|array|min:1',
        ];

        foreach ($this->criteria as $index => $criterion) {
            $rules["criteria.{$index}.name_key"] = [
                'nullable',
                Rule::in(array_keys($this->availableCriteriaNames)),
                Rule::requiredIf(empty($this->criteria[$index]['custom_name_input'])),
            ];
            $rules["criteria.{$index}.custom_name_input"] = [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(empty($this->criteria[$index]['name_key'])),
            ];
            $rules["criteria.{$index}.weight"] = 'required|numeric|min:0|max:1';
            $rules["criteria.{$index}.type"] = ['required', Rule::in(['benefit', 'cost'])];
            $rules["criteria.{$index}.data_type"] = ['required', Rule::in(['numeric', 'qualitative_option', 'qualitative_text'])];

            // Validation for value_scale if it's present (typically for predefined)
            if (isset($this->criteria[$index]['value_scale'])) {
                $rules["criteria.{$index}.value_scale.min"] = 'required|numeric';
                $rules["criteria.{$index}.value_scale.max"] = 'required|numeric|gte:criteria.'.$index.'.value_scale.min';
            }

            if (($this->criteria[$index]['data_type'] ?? null) === 'qualitative_option') {
                $rules["criteria.{$index}.options"] = 'required|array|min:1';
                foreach (($this->criteria[$index]['options'] ?? []) as $optIndex => $option) {
                    $rules["criteria.{$index}.options.{$optIndex}.label"] = 'required|string|max:255';
                    $rules["criteria.{$index}.options.{$optIndex}.value"] = 'required|string|max:255';
                    $rules["criteria.{$index}.options.{$optIndex}.numeric_value"] = 'required|numeric';
                }
            }

            if (($this->criteria[$index]['data_type'] ?? null) === 'qualitative_text') {
                $rules["criteria.{$index}.value_map"] = 'required|array|min:1';
                foreach (($this->criteria[$index]['value_map'] ?? []) as $vmIndex => $vmEntry) {
                    $rules["criteria.{$index}.value_map.{$vmIndex}.key_input"] = 'required|string|max:255';
                    $rules["criteria.{$index}.value_map.{$vmIndex}.value_input"] = 'required|numeric';
                }
            }
        }
        return $rules;
    }

    protected function messages(): array
    {
        $messages = [
            'name.required' => 'The batch name is required.',
            'start_date.required' => 'The start date is required.',
            'end_date.required' => 'The end date is required.',
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
            'criteria.required' => 'At least one criterion is required.',
            'criteria.min' => 'At least one criterion is required.',
        ];

        foreach ($this->criteria as $index => $criterion) {
            $ordinal = $index + 1;
            $messages["criteria.{$index}.name_key.required_if"] = "Criterion {$ordinal}: Predefined name or custom name is required.";
            $messages["criteria.{$index}.custom_name_input.required_if"] = "Criterion {$ordinal}: Custom name or predefined name is required.";
            $messages["criteria.{$index}.weight.required"] = "Criterion {$ordinal}: Weight is required.";
            $messages["criteria.{$index}.weight.numeric"] = "Criterion {$ordinal}: Weight must be a number.";
            $messages["criteria.{$index}.weight.min"] = "Criterion {$ordinal}: Weight must be at least 0.";
            $messages["criteria.{$index}.weight.max"] = "Criterion {$ordinal}: Weight must be at most 1.";
            $messages["criteria.{$index}.type.required"] = "Criterion {$ordinal}: Type is required.";
            $messages["criteria.{$index}.data_type.required"] = "Criterion {$ordinal}: Data type is required.";

            if (($this->criteria[$index]['data_type'] ?? null) === 'qualitative_option') {
                $messages["criteria.{$index}.options.required"] = "Criterion {$ordinal}: At least one option is required for qualitative (option) type.";
                foreach (($this->criteria[$index]['options'] ?? []) as $optIndex => $option) {
                    $optOrdinal = $optIndex + 1;
                    $messages["criteria.{$index}.options.{$optIndex}.label.required"] = "Criterion {$ordinal}, Option {$optOrdinal}: Label is required.";
                    $messages["criteria.{$index}.options.{$optIndex}.value.required"] = "Criterion {$ordinal}, Option {$optOrdinal}: Value is required.";
                    $messages["criteria.{$index}.options.{$optIndex}.numeric_value.required"] = "Criterion {$ordinal}, Option {$optOrdinal}: Numeric value is required.";
                    $messages["criteria.{$index}.options.{$optIndex}.numeric_value.numeric"] = "Criterion {$ordinal}, Option {$optOrdinal}: Numeric value must be a number.";
                }
            }
            if (($this->criteria[$index]['data_type'] ?? null) === 'qualitative_text') {
                $messages["criteria.{$index}.value_map.required"] = "Criterion {$ordinal}: At least one value map entry is required for qualitative (text) type.";
                 foreach (($this->criteria[$index]['value_map'] ?? []) as $vmIndex => $vmEntry) {
                    $vmOrdinal = $vmIndex + 1;
                    $messages["criteria.{$index}.value_map.{$vmIndex}.key_input.required"] = "Criterion {$ordinal}, Map {$vmOrdinal}: Key is required.";
                    $messages["criteria.{$index}.value_map.{$vmIndex}.value_input.required"] = "Criterion {$ordinal}, Map {$vmOrdinal}: Value is required.";
                    $messages["criteria.{$index}.value_map.{$vmIndex}.value_input.numeric"] = "Criterion {$ordinal}, Map {$vmOrdinal}: Value must be a number.";
                }
            }
        }
        return $messages;
    }

    public function update(): void
    {
        $this->validate();

        $totalWeight = array_sum(array_column($this->criteria, 'weight'));
        if (round($totalWeight, 2) !== 1.00) {
            $this->addError('criteria_total_weight', 'The total weight of all criteria must be exactly 1.0. Current total: ' . $totalWeight);
            return;
        }

        $formattedCriteria = [];
        $generatedIds = [];

        foreach ($this->criteria as $criterionData) {
            $actual_id = '';
            $actual_name = '';
            // value_scale will be handled by getPredefinedCriteriaConfig or not included for custom

            if (!empty($criterionData['name_key']) && isset($this->availableCriteriaNames[$criterionData['name_key']])) {
                // Use predefined criteria
                $actual_id = $criterionData['name_key'];
                // $actual_name will be set by getPredefinedCriteriaConfig

                // Get predefined configuration (this now includes value_scale)
                $predefinedConfig = $this->predefinedCriteriaService->getPredefinedCriteriaConfig(
                    $actual_id,
                    (float)($criterionData['weight'] ?? 0)
                );

                if ($predefinedConfig) {
                    $formattedCriteria[] = $predefinedConfig; // This carries over name, type, data_type, value_scale, options
                    continue;
                }
                // Fallback if getPredefinedCriteriaConfig returns null (should ideally not happen for valid key)
                // If it does, we might need to reconstruct, but the current structure relies on getPredefinedCriteriaConfig
                // For safety, ensure $actual_name is set if we were to proceed without $predefinedConfig
                 $actual_name = $this->availableCriteriaNames[$criterionData['name_key']] ?? 'Error: Name not found';

            } elseif (!empty($criterionData['custom_name_input'])) {
                // If it's an existing custom criterion, try to reuse its original db_id if it hasn't changed significantly.
                // For simplicity here, we'll regenerate if the name changes, or use db_id if name is same.
                // A more robust system might track if the custom_name_input itself has changed from the one that generated db_id.
                if (!empty($criterionData['db_id']) && ($criterionData['custom_name_input'] === $criterionData['display_name'])) {
                    $actual_id = $criterionData['db_id'];
                } else {
                    $slug = Str::slug($criterionData['custom_name_input']);
                    $originalSlug = $slug;
                    $counter = 1;
                    while (in_array($slug, $generatedIds) || ($slug === ($criterionData['db_id'] ?? null) && $criterionData['custom_name_input'] !== $criterionData['display_name'])) {
                        $slug = $originalSlug . '-' . $counter++;
                    }
                    $actual_id = $slug;
                }
                $actual_name = $criterionData['custom_name_input'];
            } else {
                session()->flash('error', 'A criterion was missing its name and could not be saved.');
                continue;
            }

            $generatedIds[] = $actual_id;

            $newItem = [
                'id' => $actual_id,
                'name' => $actual_name,
                'weight' => (float) ($criterionData['weight'] ?? 0),
                'type' => $criterionData['type'] ?? 'benefit',
                'data_type' => $criterionData['data_type'] ?? 'numeric',
            ];

            if ($newItem['data_type'] === 'qualitative_option' && !empty($criterionData['options']) && is_array($criterionData['options'])) {
                $newItem['options'] = array_values(array_map(function ($opt) {
                    return [
                        'label' => $opt['label'] ?? '',
                        'value' => $opt['value'] ?? ($opt['label'] ?? ''),
                        'numeric_value' => isset($opt['numeric_value']) ? (float) $opt['numeric_value'] : null,
                    ];
                }, array_filter($criterionData['options'], fn($opt) => !empty($opt['label']) && isset($opt['numeric_value']) && $opt['numeric_value'] !== '')));
                if (empty($newItem['options'])) unset($newItem['options']);
            }

            if ($newItem['data_type'] === 'qualitative_text' && !empty($criterionData['value_map']) && is_array($criterionData['value_map'])) {
                $db_value_map = [];
                foreach ($criterionData['value_map'] as $map_entry) {
                    if (!empty($map_entry['key_input']) && isset($map_entry['value_input']) && $map_entry['value_input'] !== '') {
                        $db_value_map[$map_entry['key_input']] = (float) $map_entry['value_input'];
                    }
                }
                if (!empty($db_value_map)) {
                    $newItem['value_map'] = $db_value_map;
                }
            }
            $formattedCriteria[] = $newItem;
        }

        if (empty($formattedCriteria) && !empty($this->criteria)) {
             session()->flash('error', 'No valid criteria were processed. Please check your criteria inputs.');
             return;
        }

        $this->batch->update([
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'criteria_config' => $formattedCriteria,
            // status might be updated elsewhere or based on dates, not directly here unless specified
        ]);

        session()->flash('message', 'Scholarship batch updated successfully.');
        $this->redirectRoute('admin.scholarship-batches.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.scholarship-batches.edit-scholarship-batch');
    }
}
