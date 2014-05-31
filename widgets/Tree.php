<?php
/**
 * Created by PhpStorm.
 * User: supreme
 * Date: 09.05.14
 * Time: 15:53
 */

namespace wbl\tree\widgets;

use wbl\tree\components\TreeNode;
use yii\base\Widget;
use \wbl\tree\components\Tree as TreeModel;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Tree extends Widget {

	/**
	 * Дерево элементов.
	 * @var TreeModel
	 */
	public $tree;


	/**
	 * Тег контейнера дерева.
	 * @var string
	 */
	public $tag = 'div';

	/**
	 * HTML опции контейнера дерева.
	 * @var array
	 */
	public $options = [];


	/**
	 * Тег контейнера списка.
	 * @var string
	 */
	public $listTag = 'ul';

	/**
	 * Шаблон контейнера списка.
	 * @var string
	 */
	public $listTemplate = '{nodes}';

	/**
	 * Опции контейнера списка.
	 * @var array
	 */
	public $listOptions = [];


	/**
	 * Тег контейнера элемента.
	 * @var string
	 */
	public $nodeTag = 'li';

	/**
	 * Шаблон контейнера элемента.
	 * @var string
	 */
	public $nodeTemplate = '{data}{children}';

	/**
	 * Опции контейнера элемента.
	 * @var array
	 */
	public $nodeOptions = [];


	/**
	 * Тег контейнера данных.
	 * @var string
	 */
	public $nodeDataTag = 'span';

	/**
	 * Шаблон контейнера данных.
	 * @var string
	 */
	public $nodeDataTemplate = '{value}';

	/**
	 * HTML опции контейнера данных.
	 * @var array
	 */
	public $nodeDataOptions = [];

	/**
	 * Ключ значения данных.
	 * @var string|callable
	 */
	public $nodeDataKey = 'name';


	/**
	 * @param ActiveRecord[] $models
	 */
	public function setModels($models) {
		$this->tree = new TreeModel();
		$this->tree->set($models);
	}


	/**
	 * @inheritdoc
	 */
	public function run() {
		$content = $this->renderList($this->tree->getRoot()->children);

		return Html::tag($this->tag, $content, $this->options);
	}

	/**
	 * @param TreeNode[] $nodes
	 * @return string
	 */
	public function renderList($nodes) {
		$list = [];
		foreach($nodes as $node) {
			$list[] = $this->renderNode($node);
		}

		$content = strtr($this->listTemplate, [
			'{nodes}' => implode('', $list),
		]);

		return Html::tag($this->listTag, $content, $this->listOptions);
	}

	/**
	 * @param TreeNode $node
	 * @return string
	 */
	public function renderNode($node) {
		$content = strtr($this->nodeTemplate, [
			'{data}' => $this->renderNodeData($node->data),
			'{children}' => $this->renderList($node->getChildren()),
		]);

		return Html::tag($this->nodeTag, $content, $this->nodeOptions);
	}

	/**
	 * @param ActiveRecord $data
	 * @return string
	 */
	public function renderNodeData($data) {
		$content = strtr($this->nodeDataTemplate, [
			'{value}' => ArrayHelper::getValue($data, $this->nodeDataKey),
		]);

		return Html::tag($this->nodeDataTag, $content, $this->nodeDataOptions);
	}
}