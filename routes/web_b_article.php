<?php
Route::group([
	'prefix' => 'article',
	'namespace' => 'Article'
], function () {
	Route::group([
		'prefix' => 'acategory',
	], function () {
		// 文章分类列表
		Route::get('/', 'AcategoryController@index')->name('admin.article.acategory');
		// 添加文章分类
		Route::get('/add/{cate_id?}', 'AcategoryController@add')->name('admin.article.acategory.add');
		// 编辑文章分类
		Route::get('/edit', 'AcategoryController@edit')->name('admin.article.acategory.edit');
		// 删除文章分类
		Route::get('/delete/{cate_id}', 'AcategoryController@delete')->name('admin.article.acategory.delete');
		// 保存文章分类
		Route::post('/save', 'AcategoryController@save')->name('admin.article.acategory.save');
	});

	Route::group([
		'prefix' => 'article',
	], function () {
		// 文章列表
		Route::get('/', 'ArticleController@index')->name('admin.article.article');
		// 添加文章
		Route::get('/add', 'ArticleController@add')->name('admin.article.article.add');
		// 编辑文章
		Route::get('/edit/{article_id}', 'ArticleController@edit')->name('admin.article.article.edit');
		// 删除文章
		Route::get('/delete/{article_id?}', 'ArticleController@delete')->name('admin.article.article.delete');
		// 保存文章
		Route::post('/save', 'ArticleController@save')->name('admin.article.article.save');
		// 文章批量操作
		Route::get('/batch/{field}/{value}/{ids?}', 'ArticleController@batch')->name('admin.article.article.batch');
	});

	Route::group([
		'prefix' => 'comment',
	], function () {
		// 评论列表
		Route::get('/{status?}', 'CommentController@index')->name('admin.article.comment');
		// 删除评论
		Route::get('/delete/{id?}', 'CommentController@delete')->name('admin.article.comment.delete');
		// 评论批量审核
		Route::get('/pass/{ids?}', 'CommentController@pass')->name('admin.article.comment.pass');
	});
});