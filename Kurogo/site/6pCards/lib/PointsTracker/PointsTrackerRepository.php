<?php

Kurogo::includePackage('Utilities');

class PointsTracker extends Repository{
	
	protected function init($options){
		$this->createTables();
	}
	
	private function createTables(){
//		$conn = self::connection();
//		$checkSql = "SELECT 1 FROM score";
//
//		if (!$result = $conn->query($checkSql, array(), db::IGNORE_ERRORS)) {
//			$createSQL = file_get_contents(DATA_DIR . "/PointsTracker/create_tables.sql");
//			$conn->query($createSQL);
//		}

		return true;
	}



	
	
}