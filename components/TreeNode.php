<?php
/**
 * Created by:  Pavel Kondratenko
 * Created at:  17:18 09.04.14
 * Contact:     gustarus@gmail.com
 */

namespace wbl\tree\components;

use yii\base\Object;
use yii\helpers\VarDumper;

/**
 * Class TreeNode
 * @package tree
 *
 * @property TreeNode $parent
 * @property TreeNode[] $children
 * @property TreeNode[] $path
 * @property mixed[] $pathData
 */
class TreeNode extends Object {

	/**
	 * Идентификатор элемента дерева.
	 * @var mixed
	 */
	public $pk;

	/**
	 * Ссылка на экземпляр данных.
	 * @var mixed
	 */
	public $data;

	/**
	 * Ссылка на родительский элемент.
	 * @var TreeNode
	 */
	private $parent;

	/**
	 * Коллекция дочерних элементов.
	 * @var array[TreeNode]
	 */
	private $children = [];


	/**
	 * Привязывает подительский элемент к элементу.
	 * @param TreeNode $node
	 */
	public function bindParent(TreeNode $node) {
		$this->unbind();
		$this->parent = $node;
		$this->parent->bindChild($this);
	}

	/**
	 * Отвязывает родительский элемент от элемента.
	 */
	public function unbindParent() {
		$this->parent = null;
	}

	/**
	 * Возвращает привязанного родителя.
	 * @return TreeNode
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Добавляет дочерний элемент к элементу.
	 * @param TreeNode $node
	 */
	public function addChild(TreeNode $node) {
		$this->unbind();
		$this->bindChild($node);
		$node->bindParent($this);
	}

	/**
	 * Привязывает дочерний элемент к элементу.
	 * @param TreeNode $node
	 */
	public function bindChild(TreeNode $node) {
		$this->children[$node->pk] = $node;
	}

	/**
	 * @param TreeNode $node
	 */
	public function unbindChild(TreeNode $node) {
		unset($this->children[$node->pk]);
	}

	/**
	 * Возвращает привязанных детей.
	 * @return TreeNode[]
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * Возвращает привязанных детей рекурсивно.
	 * @return TreeNode[]
	 */
	public function getAllChildren() {
		$children = $this->children;
		foreach($this->children as $child) {
			$children = array_merge($children, $child->getAllChildren());
		}

		return $children;
	}

	/**
	 * Отвязывает элемент от дерева.
	 */
	public function unbind() {
		if($this->parent) {
			$this->parent->unbindChild($this);
			$this->unbindParent();
		}
	}

	/**
	 * Возвращает уровень вложенности элемента.
	 * @return int
	 */
	public function getLevel() {
		$level = 0;
		$parent = $this;
		while($parent = $parent->parent) {
			$level++;
		}

		return $level;
	}

	/**
	 * Возвращает путь к элементу из элементов.
	 * @param bool $with_current
	 * @return TreeNode[]
	 */
	public function getPath($with_current = true) {
		$path = $with_current ? [$this] : [];
		$parent = $this->getParent();
		while($parent && $parent->pk) {
			array_unshift($path, $parent);
			$parent = $parent->getParent();
		}

		return $path;
	}

	/**
	 * Возвращает путь к элементу из данных.
	 * @param bool $with_current
	 * @return array
	 */
	public function getPathData($with_current = true) {
		$path = [];
		foreach($this->getPath($with_current) as $node) {
			$path[] = $node->data;
		}

		return $path;
	}
} 