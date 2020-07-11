<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	'accepted' => 'The :attribute must be accepted.',
	'active_url' => 'The :attribute is not a valid URL.',
	'after' => 'The :attribute must be a date after :date.',
	'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
	'alpha' => 'The :attribute may only contain letters.',
	'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
	'alpha_num' => 'The :attribute may only contain letters and numbers.',
	'array' => ':attribute数据格式错误',
	'before' => 'The :attribute must be a date before :date.',
	'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
	'between' => [
		'numeric' => 'The :attribute must be between :min and :max.',
		'file' => 'The :attribute must be between :min and :max kilobytes.',
		'string' => 'The :attribute must be between :min and :max characters.',
		'array' => 'The :attribute must have between :min and :max items.',
	],
	'boolean' => 'The :attribute field must be true or false.',
	'confirmed' => 'The :attribute confirmation does not match.',
	'date' => 'The :attribute is not a valid date.',
	'date_format' => 'The :attribute does not match the format :format.',
	'different' => 'The :attribute and :other must be different.',
	'digits' => 'The :attribute must be :digits digits.',
	'digits_between' => ':attribute长度必须介于:min到:max位之间',
	'dimensions' => 'The :attribute has invalid image dimensions.',
	'distinct' => 'The :attribute field has a duplicate value.',
	'email' => '请输入正确的邮箱地址',
	'mobile' => '请输入正确的手机号',
	'exists' => 'The selected :attribute is invalid.',
	'file' => '请上传相关附件',
	'filled' => 'The :attribute field must have a value.',
	'gt' => [
		'numeric' => 'The :attribute must be greater than :value.',
		'file' => 'The :attribute must be greater than :value kilobytes.',
		'string' => 'The :attribute must be greater than :value characters.',
		'array' => 'The :attribute must have more than :value items.',
	],
	'gte' => [
		'numeric' => 'The :attribute must be greater than or equal :value.',
		'file' => 'The :attribute must be greater than or equal :value kilobytes.',
		'string' => 'The :attribute must be greater than or equal :value characters.',
		'array' => 'The :attribute must have :value items or more.',
	],
	'image' => 'The :attribute must be an image.',
	'in' => '请输入有效的:attribute',
	'in_array' => 'The :attribute field does not exist in :other.',
	'integer' => ':attribute错误',
	'ip' => '请填写正确的IP地址',
	'ipv4' => 'The :attribute must be a valid IPv4 address.',
	'ipv6' => 'The :attribute must be a valid IPv6 address.',
	'json' => 'The :attribute must be a valid JSON string.',
	'lt' => [
		'numeric' => 'The :attribute must be less than :value.',
		'file' => 'The :attribute must be less than :value kilobytes.',
		'string' => 'The :attribute must be less than :value characters.',
		'array' => 'The :attribute must have less than :value items.',
	],
	'lte' => [
		'numeric' => 'The :attribute must be less than or equal :value.',
		'file' => 'The :attribute must be less than or equal :value kilobytes.',
		'string' => 'The :attribute must be less than or equal :value characters.',
		'array' => 'The :attribute must not have more than :value items.',
	],
	'max' => [
		'numeric' => ':attribute不能大于:max',
		'file' => 'The :attribute may not be greater than :max kilobytes.',
		'string' => ':attribute不能超过:max个字符',
		'array' => 'The :attribute may not have more than :max items.',
	],
	'mimes' => 'The :attribute must be a file of type: :values.',
	'mimetypes' => 'The :attribute must be a file of type: :values.',
	'min' => [
		'numeric' => ':attribute不能小于:min',
		'file' => 'The :attribute must be at least :min kilobytes.',
		'string' => ':attribute至少需要输入:min个字符',
		'array' => 'The :attribute must have at least :min items.',
	],
	'not_in' => 'The selected :attribute is invalid.',
	'not_regex' => 'The :attribute format is invalid.',
	'numeric' => ':attribute必须为一个数字',
	'present' => 'The :attribute field must be present.',
	'regex' => 'The :attribute format is invalid.',
	'required' => ':attribute不能为空',
	'required_if' => 'The :attribute field is required when :other is :value.',
	'required_unless' => 'The :attribute field is required unless :other is in :values.',
	'required_with' => 'The :attribute field is required when :values is present.',
	'required_with_all' => 'The :attribute field is required when :values is present.',
	'required_without' => 'The :attribute field is required when :values is not present.',
	'required_without_all' => 'The :attribute field is required when none of :values are present.',
	'same' => 'The :attribute and :other must match.',
	'size' => [
		'numeric' => 'The :attribute must be :size.',
		'file' => 'The :attribute must be :size kilobytes.',
		'string' => 'The :attribute must be :size characters.',
		'array' => 'The :attribute must contain :size items.',
	],
	'string' => 'The :attribute must be a string.',
	'timezone' => 'The :attribute must be a valid zone.',
	'unique' => '您输入的:attribute已存在，请重新输入',
	'uploaded' => 'The :attribute failed to upload.',
	'url' => 'The :attribute format is invalid.',

	'captcha' => '验证码错误，请重新填写',
	'idcard' => '身份证错误，请重新填写',

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => [
		'password' => [
			'confirmed' => '两次输入的密码不一致'
		],
		'privs' => [
			'required' => '请选择权限'
		],
		'groupid' => [
			'required' => '请选择一个用户组'
		],
		'map_path'=>[
			'required'=>'请上传svg地图'
		],
		'exhibition_id'=>[
			'required'=>'请选择所属展览'
		],
		'map_id'=>[
			'required'=>'请选择所属地图'
		],
		'x'=>[
			'required'=>'请标注所在点位'
		],
		'y'=>[
			'required'=>'请标注所在点位'
		],
		'road_exhibit_id'=>[
			'required'=>'请选择路线上的展品'
		],
		'auto_exhibit_id'=>[
			'required'=>'请选择要关联的展品'
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => [
		'nickname'=>'姓名',
		'username' => '用户名',
		'password' => '密码',
		'phone' => '手机号',
		'email' => '邮箱地址',
		'captcha' => '验证码',
		'groupname' => '用户组名称',
		'cate_name' => '分类名称',
		'title' => '标题',
		'cate_id' => '分类',
		'content' => '内容',
		'old_password' => '原密码',
		'smscode' => '短信验证码',
		'map_name'=>'地图名称',
		'exhibition_name_1'=>'展览中文名称',
		'exhibit_num'=>'展品编号',
		'autonum'=>'多模蓝牙编号',
		'exhibit_name_1'=>'展品中文名称',
	],

];
