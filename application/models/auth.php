<?phpclass auth extends CI_Model {    function __construct(){        parent::__construct();    }	    function login($user,$pass) {		$sql = "SELECT * FROM sys_user WHERE username='$user'";		$query = $this->db->query($sql);				if($query->result() == array()){			return false;		}else{			foreach($query->result() as $row){				if($row->password == md5($pass)){										$data = array(						'login'				=> true,						'id'				=> $row->id,						'username'			=> $row->username,						'group'				=> $row->group_id,						'nama'				=> $row->nama,						'tipe'				=> sha1(md5('7')),					);					$this->session->set_userdata($data);					return true;				}				return false;			}		}		return false;	}	function userLogin(){		return $this->db->get_where('sys_user',array('id'=>$this->session->userdata('id')))->row_array();	}	function parent_menu(){		$group_id = $this->session->userdata('group');		$sql="select * from sys_menu 			where parent_id=0 and id in 			(select menu_id from sys_user_group where group_id='$group_id') order by urutan";		$result = $this->db->query($sql)->result_array();		return $result;	}		function child_menu(){		$group_id = $this->session->userdata('group');		$sql="select * from sys_menu 			where parent_id > 0 and id in 			(select menu_id from sys_user_group where group_id=$group_id) order by urutan";		$result = $this->db->query($sql)->result_array();		return $result;	}	function list_menu($data,$parent_id,$level){		echo "<ul>";		foreach($data as $row){			if($row['PARENT_ID'] == $parent_id and $row['LEVEL'] == ($level+1)){				if($row['URL'] == '#'){					echo "<li data-options=\"state:'closed',attributes:'parent',iconCls:'$row[ICON]'\">";					echo " <span> $row[NAMA]</span>";					$this->list_menu($data,$row['ID'],$row['LEVEL']);					echo "</li>";				}else{					echo "<li data-options=\"iconCls:'$row[ICON]',attributes:'$row[URL]'\" onclick=\"t_menu('$row[URL]')\"> $row[NAMA]</li>";				}			}		}		echo "</ul>";	}	function pengguna(){		$where = array('id'=>$this->session->userdata('id'));		$this->db->where($where);		$result = $this->db->get('sys_user')->row();		return $result;	}	function profil(){		return $this->db->get('profil')->row();	}	public function kategoriProduk()	{		$sql = "select * from kategori_barang where id in (select kategori_barang_id from barang where ready_stock = 1) and id <> 9 order by id asc";		return $this->db->query($sql)->result_array();	}	public function carousel_home()	{		return $this->db->get('slide_show')->result_array();	}	public function landing_page($limit)	{		$sql = "select * from barang order by id desc limit $limit";		return $this->db->query($sql)->result_array();	}	public function getBarang($kategoriBarangId)	{		$sql = "select * from barang where kategori_barang_id = '$kategoriBarangId' order by id desc limit 4";		return $this->db->query($sql)->result_array();	}	public function recomendedItem($param)	{		$sql = "select * from barang where recomended_item = 1 order by id desc limit $param";		return $this->db->query($sql)->result_array();	}	public function bannerLoad($where)	{		$sql = "select * from banner where is_aktif = 1 $where order by id desc";		return $this->db->query($sql)->result_array();	}	public function getReviews($barang_id)	{		$this->db->where(array('barang_id'=>$barang_id));		return $this->db->get('reviews')->result_array();	}	public function menyimpan($tabel)	{		$this->simpan($tabel);	}	public function infoPembayaran($value='')	{		return $this->db->get_where('artikel',array('id'=>1))->row_array();	}	public function dashboard($value='')	{		$sql = "select * from shop where status_order = 1";		$r['new_order'] = $this->db->query($sql)->num_rows();		$sql = "select * from pelanggan";		$r['member'] = $this->db->query($sql)->num_rows();		$sql = "select * from barang";		$r['barang'] = $this->db->query($sql)->num_rows();		$sql = "select sum(qty) qty from shop_detail where shop_id in (select id from shop where status_order = 5)";		$r['sales_item'] = $this->db->query($sql)->row_array();		$sql = "select sum(total) total from shop where status_order = 5";		$r['total_pendapatan'] = $this->db->query($sql)->row_array();		$bulan = date("m");		$tahun = date("Y");		$sql = "select sum(total) total from shop where status_order = 5 and date_format(tanggal,'%m') = '$bulan' and date_format(tanggal,'%Y') = '$tahun'";		$r['total_pendapatan_bulan_ini'] = $this->db->query($sql)->row_array();		$sql = "select * from pesan where status = 0 and parent_id = 0";		$r['notifikasi'] = $this->db->query($sql)->num_rows();		$sql = "select * from shop where status_order = 1 order by tanggal desc limit 10";		$r['last_order'] = $this->db->query($sql)->result_array();		return $r;	}}?>