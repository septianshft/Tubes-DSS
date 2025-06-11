<?php

namespace App\Livewire\Admin\ScholarshipBatches;

use App\Models\ScholarshipBatch;
use App\Services\PredefinedCriteriaService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule; // Added for validation

#[Layout('components.layouts.app')]
class CreateScholarshipBatch extends Component
{
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

    public function mount()
    {
        // Load predefined criteria names
        $this->availableCriteriaNames = $this->predefinedCriteriaService->getAvailableCriteriaNames();

        // Initialize with one empty criterion row if criteria array is empty
        if (empty($this->criteria)) {
            $this->addCriterion();
        }
    }

    public function addCriterion(): void
    {
        $this->criteria[] = [
            'component_id' => 'crit_' . Str::random(8),     // Unique ID for wire:key and temporary reference
            'name_key' => '',                               // Stores the key if a predefined name is selected (e.g., 'average_score')
            'custom_name_input' => '',                      // Stores the name if entered manually by the user
            'display_name' => 'New Criterion',              // UI display name, derived from name_key or custom_name_input
            'weight' => null,                               // Default to null, user must input
            'type' => 'benefit',                            // Default type
            'data_type' => 'numeric',                       // Default data_type
            'options_config_type' => 'none',                // UI helper: 'none', 'options', 'value_map'
            'options' => [],                                // For data_type: qualitative_option, structure: [['label' => '', 'value' => '', 'numeric_value' => null]]
            'value_map' => [],                              // For data_type: qualitative_text (form structure: array of ['key_input'=>'','value_input'=>null])
        ];
    }

    public function removeCriterion(int $index): void
    {
        unset($this->criteria[$index]);
        $this->criteria = array_values($this->criteria); // Re-index array
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
                $criterion['custom_name_input'] = ''; // Clear custom input
                $criterion['display_name'] = $this->availableCriteriaNames[$criterion['name_key']] ?? 'Unknown Criterion';

                $predefinedConfig = $this->predefinedCriteriaService->getCriteriaDefinition($criterion['name_key']);
                if ($predefinedConfig) {
                    $criterion['type'] = $predefinedConfig['type'];
                    $criterion['data_type'] = $predefinedConfig['data_type'];
                    $criterion['is_predefined'] = true;

                    if (isset($predefinedConfig['value_scale'])) {
                        $criterion['value_scale'] = $predefinedConfig['value_scale'];
                    } else {
                        unset($criterion['value_scale']);
                    }

                    // Reset options and value_map before populating based on new data_type
                    $criterion['options'] = [];
                    $criterion['value_map'] = [];
                    $criterion['options_config_type'] = 'none'; // Default

                    if ($criterion['data_type'] === 'qualitative_option' && isset($predefinedConfig['options'])) {
                        $criterion['options_config_type'] = 'options';
                        // Assuming $predefinedConfig['options'] is already in the correct format:
                        // [['label' => 'Excellent', 'value' => 'excellent', 'numeric_value' => 5], ...]
                        $criterion['options'] = $predefinedConfig['options'];
                    } elseif ($criterion['data_type'] === 'qualitative_text' && isset($predefinedConfig['value_map'])) {
                        $criterion['options_config_type'] = 'value_map';
                        // Assuming $predefinedConfig['value_map'] is like: ['High' => 3, 'Medium' => 2]
                        // Transform to the component's expected format: [['key_input' => 'High', 'value_input' => 3], ...]
                        $tempValueMap = [];
                        foreach ($predefinedConfig['value_map'] as $vmKey => $vmValue) {
                            $tempValueMap[] = ['key_input' => (string)$vmKey, 'value_input' => $vmValue];
                        }
                        $criterion['value_map'] = $tempValueMap;
                    }
                    // If not qualitative_option or qualitative_text with definitions,
                    // options_config_type remains 'none' and options/value_map remain empty.

                } else {
                    // Config not found for name_key, potentially reset to defaults or treat as error
                    $criterion['is_predefined'] = false;
                    unset($criterion['value_scale']);
                    // Consider if type/data_type should be reset to defaults here
                    // For now, they retain their previous values if config not found
                }
            } elseif (empty($criterion['custom_name_input'])) {
                // Both name_key and custom_name_input are empty
                $criterion['display_name'] = 'New Criterion';
                $criterion['is_predefined'] = false;
                unset($criterion['value_scale']);
                // Optionally reset type, data_type, options, value_map to defaults
                // $criterion['type'] = 'benefit';
                // $criterion['data_type'] = 'numeric';
                // $criterion['options_config_type'] = 'none';
                // $criterion['options'] = [];
                // $criterion['value_map'] = [];
            }
        } elseif ($field === 'custom_name_input') {
            if (!empty($criterion['custom_name_input'])) {
                $criterion['name_key'] = ''; // Clear predefined selection
                $criterion['display_name'] = $criterion['custom_name_input'];
                $criterion['is_predefined'] = false;
                unset($criterion['value_scale']);
                // Reset to defaults for custom criteria - type/data_type might be kept or reset based on desired UX
                // $criterion['type'] = 'benefit'; // Or keep existing
                // $criterion['data_type'] = 'numeric'; // Or keep existing
                $criterion['options_config_type'] = 'none';
                $criterion['options'] = [];
                $criterion['value_map'] = []; // Ensure value_map is also cleared
            } elseif (empty($criterion['name_key'])) {
                // Both custom_name_input and name_key are empty
                $criterion['display_name'] = 'New Criterion';
            }
        } elseif ($field === 'data_type') {
            // This block handles manual changes to data_type
            // It correctly resets options/value_map and sets options_config_type
            // and adds a default entry if applicable.
            $criterion['options'] = [];
            $criterion['value_map'] = [];
            if ($criterion['data_type'] === 'qualitative_option') {
                $criterion['options_config_type'] = 'options';
                $this->addOption($index); // Add one empty option
            } elseif ($criterion['data_type'] === 'qualitative_text') {
                $criterion['options_config_type'] = 'value_map';
                $this->addValueMapEntry($index); // Add one empty value map entry
            } else {
                $criterion['options_config_type'] = 'none';
            }
        }
        unset($criterion);
    }

    // updateDisplayName is effectively handled by updatedCriteria now.
    // public function updateDisplayName(int $index): void
    // {
    // ...
    // }

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

            // Add validation for value_scale if it's set (e.g. for predefined criteria)
            // This is more for ensuring data integrity if it were to be user-editable,
            // but good to have if the structure is expected.
            if (isset($this->criteria[$index]['value_scale'])) {
                $rules["criteria.{$index}.value_scale.min"] = 'required|numeric';
                $rules["criteria.{$index}.value_scale.max"] = 'required|numeric|gte:criteria.'.$index.'.value_scale.min';
            }

            if (($this->criteria[$index]['data_type'] ?? null) === 'qualitative_option') {
                $rules["criteria.{$index}.options"] = 'required|array|min:1';
                foreach (($this->criteria[$index]['options'] ?? []) as $optIndex => $option) {
                    $rules["criteria.{$index}.options.{$optIndex}.label"] = 'required|string|max:255';
                    $rules["criteria.{$index}.options.{$optIndex}.value"] = 'required|string|max:255'; // Or distinct if needed
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

    protected function messages(): array // Added return type
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


    public function save(): void
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
            $value_scale_to_save = null; // Initialize

            if (!empty($criterionData['name_key']) && isset($this->availableCriteriaNames[$criterionData['name_key']])) {
                $actual_id = $criterionData['name_key'];
                $actual_name = $this->availableCriteriaNames[$criterionData['name_key']];

                // Get full predefined config, which now includes value_scale
                $predefinedConfig = $this->predefinedCriteriaService->getPredefinedCriteriaConfig(
                    $actual_id,
                    (float)($criterionData['weight'] ?? 0)
                );

                if ($predefinedConfig) {
                    // The getPredefinedCriteriaConfig already formats it correctly, including value_scale
                    $formattedCriteria[] = $predefinedConfig;
                    continue;
                }
                // Fallback if getPredefinedCriteriaConfig failed (should not happen if name_key is valid)
                // This part might be redundant if getPredefinedCriteriaConfig is robust
                if (isset($criterionData['value_scale'])) { // Check if value_scale was populated in the form
                    $value_scale_to_save = $criterionData['value_scale'];
                }

            } elseif (!empty($criterionData['custom_name_input'])) {
                $slug = Str::slug($criterionData['custom_name_input']);
                $originalSlug = $slug;
                $counter = 1;
                while (in_array($slug, $generatedIds)) {
                    $slug = $originalSlug . '-' . $counter++;
                }
                $actual_id = $slug;
                $actual_name = $criterionData['custom_name_input'];
            } else {
                // This case should be prevented by validation.
                // If it occurs, log or flash an error and skip.
                session()->flash('error', 'A criterion was missing its name and could not be saved.');
                continue;
            }

            $generatedIds[] = $actual_id; // Add to list of generated/used IDs for this batch

            $newItem = [
                'id' => $actual_id,
                'name' => $actual_name,
                'weight' => (float) ($criterionData['weight'] ?? 0),
                'type' => $criterionData['type'] ?? 'benefit',
                'data_type' => $criterionData['data_type'] ?? 'numeric',
                // 'is_predefined' is implicitly handled by how $actual_id is set or by getPredefinedCriteriaConfig
            ];

            // Add value_scale if it was determined (either from predefined or potentially custom in future)
            if ($value_scale_to_save) {
                $newItem['value_scale'] = $value_scale_to_save;
            }

            // If it's a predefined criterion and we didn't use the full $predefinedConfig path,
            // ensure value_scale is added if it was set on $criterionData (e.g., by updatedCriteria)
            // This is a bit of a safeguard. The main path via getPredefinedCriteriaConfig is preferred.
            if (empty($value_scale_to_save) && !empty($criterionData['name_key']) && isset($criterionData['value_scale'])) {
                 $definition = $this->predefinedCriteriaService->getCriteriaDefinition($criterionData['name_key']);
                 if ($definition && isset($definition['value_scale'])) {
                     $newItem['value_scale'] = $definition['value_scale'];
                 }
            }


            if ($newItem['data_type'] === 'qualitative_option' && !empty($criterionData['options']) && is_array($criterionData['options'])) {
                $newItem['options'] = array_values(array_map(function ($opt) { // array_values to re-index
                    return [
                        'label' => $opt['label'] ?? '',
                        'value' => $opt['value'] ?? ($opt['label'] ?? ''),
                        'numeric_value' => isset($opt['numeric_value']) ? (float) $opt['numeric_value'] : null,
                    ];
                }, array_filter($criterionData['options'], fn($opt) => !empty($opt['label']) && isset($opt['numeric_value']) && $opt['numeric_value'] !== '')));
                 if (empty($newItem['options'])) unset($newItem['options']); // Remove if all options were invalid
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


        ScholarshipBatch::create([
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'criteria_config' => $formattedCriteria,
            'status' => 'upcoming', // Default status
        ]);

        session()->flash('message', 'Scholarship batch created successfully.');
        $this->redirectRoute('admin.scholarship-batches.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.scholarship-batches.create-scholarship-batch');
    }
}
