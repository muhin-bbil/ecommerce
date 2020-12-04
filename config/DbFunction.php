
<?php
require('Database.php');
//$db = Database::getInstance();
//$mysqli = $db->getConnection();
class DbFunction{

	function showCategories(){

		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		$query = "select category.Name, count(Item_category_relations.id) as 'total_item'
					from category
					left join Item_category_relations
					on category.id = Item_category_relations.categoryId
					group by category.id
					order by total_item desc";
		$stmt= $mysqli->query($query);
		return $stmt;

	}

	function showNestedCategories(){

		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		$query = "select category.*, category.id as cat_id, catetory_relations.ParentcategoryId
					from category
					left join catetory_relations
					on category.id = catetory_relations.categoryId
					where catetory_relations.ParentcategoryId IS NULL";
		$stmt= $mysqli->query($query);
		$list = "<ul>";
		while($res=$stmt->fetch_object()){
			$list .="<li>".$res->Name."(0)</li>";

			$child = $this->fetchChildCategoryList($res->cat_id);

			$list .= $child;
		}
		$list .= "</ul>";

		//return $stmt;
		return $list;

	}

	function fetchChildCategoryList($parent, $list_string='') {

		$db = Database::getInstance();
		$mysqli = $db->getConnection();

		$query = "select category.*, category.id as cat_id, catetory_relations.ParentcategoryId
				from category
				left join catetory_relations
    			on category.id = catetory_relations.categoryId
				WHERE catetory_relations.ParentcategoryId=$parent
    			order by category.id asc";
		$stmt= $mysqli->query($query);

		$list_string ="<ul>";
		$total_item = 0;
		while($child_res=$stmt->fetch_object()){
			$item = $this->getCatItems($child_res->cat_id);
			$total_item +=$item;
			$list_string .="<li>".$child_res->Name."(".$item.")</li>";

			//$list_string = $this->fetchChildCategoryList($child_res->cat_id, $list_string);
		}

		$list_string .="</ul>";

		return $list_string;
	}

	function getCatItems($cat_id){

		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		$query = "select category.Name, count(Item_category_relations.id) as 'total_item'
					from category
					left join Item_category_relations
					on category.id = Item_category_relations.categoryId					
				    WHERE Item_category_relations.categoryId=$cat_id";
		$stmt= $mysqli->query($query);
		$row = $stmt->fetch_assoc();
		return $row['total_item'];

	}

}

?>



