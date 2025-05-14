<?php
session_start();
class Mahasiswa {
    private $conn;
    private $table_name = "mahasiswa";
    public $id;
    public $nim;
    public $nama;
    public $jurusan;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function isDuplicate($nim, $nama, $id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE (nim = ? OR nama = ?)" . ($id ? " AND id != ?" : "");
        $stmt = $this->conn->prepare($query);
        $id ? $stmt->bind_param("sss", $nim, $nama, $id) : $stmt->bind_param("ss", $nim, $nama);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function create(){
        if ($this->isDuplicate($this->nim, $this->nama)) {
            $_SESSION['flash_message'] = "NIM atau Nama sudah ada dalam database!";
            header("Location: ".BASE_URL."index.php?msg=0");
            return;
        }
        $query = "INSERT INTO " . $this->table_name . " SET nim=?, nama=?, jurusan=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $this->nim, $this->nama, $this->jurusan);
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Data berhasil disimpan!";
            header("Location: ".BASE_URL."index.php?msg=1");
        } else {
            $_SESSION['flash_message'] = "Data gagal disimpan!";
            header("Location: ".BASE_URL."index.php?msg=0");
        }
    }

    public function read($id = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($id) {
            $query .= " WHERE id = ?";
        }
        $stmt = $this->conn->prepare($query);
        if ($id) {
            $stmt->bind_param("s", $id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function update(){
        if ($this->isDuplicate($this->nim, $this->nama, $this->id)) {
            $_SESSION['flash_message'] = "NIM atau Nama sudah ada dalam database!";
            header("Location: ".BASE_URL."index.php?msg=0");
            return;
        }
        $query = "UPDATE " . $this->table_name . " SET nim=?, nama=?, jurusan=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $this->nim, $this->nama, $this->jurusan, $this->id); 
        if($stmt->execute()){
            $_SESSION['flash_message'] = "Data berhasil diupdate!";
            header("Location: ".BASE_URL."index.php?msg=1");
        } else {
            $_SESSION['flash_message'] = "Data gagal diupdate!";
            header("Location: ".BASE_URL."index.php?msg=0");
        }
    }

    public function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->id); 
        if($stmt->execute()){
            $_SESSION['flash_message'] = "Data berhasil dihapus!";
            header("Location: ".BASE_URL."index.php?msg=1");
        } else {
            $_SESSION['flash_message'] = "Data gagal dihapus!";
            header("Location: ".BASE_URL."index.php?msg=0");
        }
    }
}
?>