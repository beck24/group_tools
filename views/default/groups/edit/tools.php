<?php

/**
 * Group edit form
 *
 * This view contains the group tool options provided by the different plugins
 *
 * @package ElggGroups
 */

$entity = elgg_extract('entity', $vars);
$tools = groups_get_group_tool_options($entity);
if (empty($tools)) {
	return;
}

// if the form comes back from a failed save we can't use the presets
$sticky_form = (bool) elgg_extract('group_tools_presets', $vars, false);
$presets = false;
if (!$sticky_form) {
	$presets = group_tools_get_tool_presets();
}

// we need to be able to tell if we came from a sticky form
echo elgg_view('input/hidden', ['name' => 'group_tools_presets', 'value' => '1']);

// new group can choose from preset (if any)
if (empty($entity) && !empty($presets)) {
	echo elgg_view('output/longtext', [
		'value' => elgg_echo('group_tools:create_group:tool_presets:description'),
	]);
	
	$preset_selectors = '';
	$preset_descriptions = '';
	
	$presets_tools = [];
	
	foreach ($presets as $index => $values) {
		$preset_selectors .= elgg_view('output/url', [
			'text' => elgg_extract('title', $values),
			'href' => '#',
			'class' => 'elgg-button elgg-button-action mrm',
			'rel' => $index,
			'title' => elgg_strip_tags(elgg_extract('description', $values)),
		]);
		
		$preset_tools = elgg_extract('tools', $values);
		foreach ($preset_tools as $tool_name => $tool) {
			if ($tool == 'yes') {
				$presets_tools[$tool_name][] = "group-tools-preset-{$index}";
			}
		}
		
		$preset_descriptions .= elgg_format_element('div', [
			'id' => "group-tools-preset-description-{$index}",
			'class' => 'hidden'], elgg_extract('description', $values));
	}
	
	// blank preset
	$preset_selectors .= elgg_view('output/url', [
		'text' => elgg_echo('group_tools:create_group:tool_presets:blank:title'),
		'href' => '#',
		'class' => 'elgg-button elgg-button-action mrm',
		'rel' => 'blank',
	]);
	$preset_descriptions .= elgg_format_element('div', [
		'id' => 'group-tools-preset-description-blank',
		'class' => 'hidden'], elgg_echo('group_tools:create_group:tool_presets:blank:description'));
	
	echo elgg_format_element('label', ['class' => 'mbs'], elgg_echo('group_tools:create_group:tool_presets:select')) . ':';
	echo elgg_format_element('div', ['id' => 'group-tools-preset-selector'], $preset_selectors);
	echo elgg_format_element('div', ['id' => 'group-tools-preset-descriptions'], $preset_descriptions);
	
	$more_link = elgg_view('output/url', [
		'text' => elgg_echo('group_tools:create_group:tool_presets:show_more'),
		'href' => '#group-tools-preset-more',
		'rel' => 'toggle',
		'class' => 'float-alt',
	]);
	echo elgg_view_module('info', elgg_echo('group_tools:create_group:tool_presets:active_header'), $more_link, ['id' => 'group-tools-preset-active', 'class' => 'hidden']);
	
	$tools_content = '';
	foreach ($tools as $group_option) {
		$group_option_toggle_name = "{$group_option->name}_enable";
		$value = 'no';
		
		$options = [
			'group_tool' => $group_option,
			'value' => $value,
			'class' => 'mbm',
		];
		
		if (array_key_exists($group_option_toggle_name, $presets_tools)) {
			$options['class'] .= ' ' . implode(' ', $presets_tools[$group_option_toggle_name]);
		}
		
		$tools_content .= elgg_view('group_tools/elements/group_tool', $options);
	}
	
	echo elgg_view_module('info', elgg_echo('group_tools:create_group:tool_presets:more_header'), $tools_content, ['id' => 'group-tools-preset-more', 'class' => 'hidden']);
	
	?>
	<script type='text/javascript'>
		require(['group_tools/ToolsPreset'], function(ToolsPreset) {
			ToolsPreset.init('#group-tools-group-edit-tools');
		});
	</script>
	<?php
} else {

	usort($tools, create_function('$a, $b', 'return strcmp($a->label, $b->label);'));
	
	foreach ($tools as $group_option) {
		$group_option_toggle_name = "{$group_option->name}_enable";
		$value = elgg_extract($group_option_toggle_name, $vars);
		
		echo elgg_view('group_tools/elements/group_tool', [
			'group_tool' => $group_option,
			'value' => $value,
		]);
	}
}
?>
<script type='text/javascript'>
	require(['group_tools/ToolsEdit'], function(ToolsEdit) {
		ToolsEdit.init('#group-tools-group-edit-tools');
	});
</script>
