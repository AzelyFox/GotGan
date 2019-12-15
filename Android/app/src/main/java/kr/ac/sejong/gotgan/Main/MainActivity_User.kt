package kr.ac.sejong.gotgan.Main

import android.app.DatePickerDialog
import android.app.ProgressDialog
import android.content.Intent
import android.os.AsyncTask
import android.os.Build
import android.os.Bundle
import android.util.Log
import android.view.MenuItem
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.annotation.RequiresApi
import androidx.appcompat.app.ActionBar
import androidx.appcompat.app.ActionBarDrawerToggle
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.widget.AppCompatSpinner
import androidx.appcompat.widget.Toolbar
import androidx.cardview.widget.CardView
import androidx.core.view.GravityCompat
import androidx.drawerlayout.widget.DrawerLayout
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.appeaser.sublimepickerlibrary.helpers.SublimeOptions
import com.google.android.material.button.MaterialButton
import com.google.android.material.navigation.NavigationView
import com.google.android.material.snackbar.Snackbar
import com.google.android.material.textfield.TextInputEditText
import com.google.android.material.textfield.TextInputLayout
import com.google.zxing.integration.android.IntentIntegrator
import com.google.zxing.integration.android.IntentResult
import kr.ac.sejong.gotgan.BackEndAPI.ProductSystem.BackEndAPI_ProductSystemProductList
import kr.ac.sejong.gotgan.BackEndAPI.RentSystem.BackEndAPI_RentSystemRentAdd
import kr.ac.sejong.gotgan.BackEndAPI.RentSystem.BackEndAPI_RentSystemRentList
import kr.ac.sejong.gotgan.BackEndAPI.productBarcodeStr
import kr.ac.sejong.gotgan.BackEndAPI.productIndexStr
import kr.ac.sejong.gotgan.BackEndAPI.productNameStr
import kr.ac.sejong.gotgan.HttpPostSendLogOut
import kr.ac.sejong.gotgan.Login.LoginActivity
import kr.ac.sejong.gotgan.R
import kr.ac.sejong.gotgan.SublimePickerDialogFragment
import kr.ac.sejong.gotgan.ZXingActivity
import org.json.JSONArray
import org.json.JSONObject
import java.time.LocalDate
import java.time.format.DateTimeFormatter

class MainActivity_User : AppCompatActivity(), NavigationView.OnNavigationItemSelectedListener {

    override fun onNavigationItemSelected(item: MenuItem): Boolean {
        var id = item.itemId

        if (id == R.id.nav_logout) {
            showProgressDialogVertical(getString(R.string.logout_trying_title), getString(R.string.logout_trying_message))
            logoutManager = LogOutManager()
            logoutManager!!.execute(session, "0", "1", "1")
        }

        drawer!!.closeDrawer(GravityCompat.START)
        return true
    }

    companion object {
        var startDateEdit : TextInputEditText? = null
        var finishDateEdit : TextInputEditText? = null
        var rentAblePeriod : Long? = null
    }

    private var logoutManager : LogOutManager? = null

    private var backKeyClickTime : Long = 0
    private var session : String? = null
    private var userIndex : String? = null
    private var rentListAndProductListManager : RentListAndProductListManager? = null
    private var userRentStatusRecyclerView : RecyclerView? = null
    private var progressDialog : ProgressDialog? = null
    private var userRentListArrayList : ArrayList<UserRentStatusData> = ArrayList()

    private var userRentGroupSpinner : AppCompatSpinner? = null
    private var userRentProductListSpinner : AppCompatSpinner? = null
    private var userRentStartDatePickerImageView : ImageView? = null
    private var userRentStartDateTextInputLayout : TextInputLayout? = null
    // private var startDateEdit : TextInputEditText? = null
    //private var finishDateEdit : TextInputEditText? = null
    private var userRentAddButton : MaterialButton? = null

    private var userRentGroupSpinnerArrayList : ArrayList<GroupListData> = ArrayList()
    private var userRentProductListSpinnerHashMap : HashMap<Int, ArrayList<ProductListData>> = HashMap()

    private var productListDataArrayListIndex : Int? = null

    // 대여 신청시 필요한 값들
    private var userRentProductListData : ProductListData? = null
    private var userRentTimeStart : String? = null

    private var rendAddManager : RentAddManager? = null

    private val fragmentManager = supportFragmentManager
    private var sublimePickerDialogFragment = SublimePickerDialogFragment()

    private var barcodeCardView : CardView? = null

    private var toolbar : Toolbar? = null
    private var drawer : DrawerLayout? = null
    private var navigationView : NavigationView? = null
    private var actionBar : ActionBar? = null

    private var userNameTextView : TextView? = null
    private var userLevelTextView : TextView? = null

    private var userName : String? = null
    private var userLevel : String? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main_user)

        session = JSONObject(intent.getStringExtra("userAllData").toString())["session"].toString()
        userIndex = JSONObject(intent.getStringExtra("userAllData").toString())["user_index"].toString()

        userName = JSONObject(intent.getStringExtra("userAllData").toString())["user_name"].toString()
        when (JSONObject(intent.getStringExtra("userAllData").toString())["user_level"].toString().toInt()) {
            0 -> userLevel = "일반 사용자"
            1 -> userLevel = "관리자"
            2 -> userLevel = "최고 관리자"
        }

        toolbarSetting()
        recyclerViewSetting()
        rentAddViewSetting()

        showProgressDialog(getString(R.string.loading_data))
        rentListAndProductListManager = RentListAndProductListManager()
        rentListAndProductListManager!!.execute(session, "1", "1", "1", userIndex)

        setSpinnerListener()
        datePickerSetting()
        rentAddButtonSetting()
        barcodeSetting()
    }

    private fun toolbarSetting() {
        toolbar = findViewById(R.id.toolbar)
        setSupportActionBar(toolbar)

        drawer = findViewById(R.id.drawer_layout)
        navigationView = findViewById(R.id.nav_view)

        setSupportActionBar(toolbar)
        actionBar = supportActionBar
        actionBar!!.setHomeAsUpIndicator(R.drawable.menu)
        actionBar!!.setDisplayHomeAsUpEnabled(true)

        userNameTextView = navigationView!!.getHeaderView(0).findViewById(R.id.userNameTextView)
        userLevelTextView = navigationView!!.getHeaderView(0).findViewById(R.id.userLevelTextView)

        userNameTextView!!.text = userName
        userLevelTextView!!.text = userLevel

        navigationView!!.setNavigationItemSelectedListener(this)
    }

    override fun onOptionsItemSelected(item: MenuItem?): Boolean {
        var id : Int = item!!.itemId

        when (id){
            android.R.id.home -> {
                drawer!!.openDrawer(GravityCompat.START)
                return true
            }
        }
        return super.onOptionsItemSelected(item)
    }

    private fun showProgressDialog(title : String) {
        progressDialog = ProgressDialog(this)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL)
        progressDialog!!.setTitle(title)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    private fun showProgressDialogVertical(title : String, message : String) {
        progressDialog = ProgressDialog(this)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_SPINNER)
        progressDialog!!.setTitle(title)
        progressDialog!!.setMessage(message)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    private fun barcodeSetting() {
        barcodeCardView = findViewById(R.id.barcode_CardView)
        barcodeCardView!!.setOnClickListener {
            var integrator = IntentIntegrator(this)
            integrator.captureActivity = ZXingActivity::class.java
            integrator.setOrientationLocked(false)
            integrator.initiateScan()
        }
    }

    @RequiresApi(Build.VERSION_CODES.O)
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        // QR코드/ 바코드를 스캔한 결과
        var result : IntentResult = IntentIntegrator.parseActivityResult(requestCode, resultCode, data)

        if (result.contents != null) {
            showProgressDialog(getString(R.string.rent_adding))
            rendAddManager = RentAddManager()
            rendAddManager!!.execute(session, "0", "1", "1", result.contents.toString(), LocalDate.now().format(DateTimeFormatter.ofPattern("yyyy-MM-dd")), "productBarcode", userRentProductListData!!.productName)
        }
        // result.getFormatName() : 바코드 종류
        // result.getContents() : 바코드 값
        //Toast.makeText(this, getString(R.string.the_barcode_value_is) + " " + result.contents.toString() + " 입니다 :)", Toast.LENGTH_LONG).show()

        // 이미 있는 바코드인지 검색한다.
        // 이미 있다면, Rent 가능한지?
        // 없다면 추가를 도와준다
        //showProgressDialog(getString(R.string.searching_barcode))
        //var barcodeSearch = ProductManagementSearchWithBarcode()
        //barcodeSearch.execute(session, "1", "1", "1", result.contents.toString())
    }

    private fun recyclerViewSetting() {
        userRentStatusRecyclerView = findViewById(R.id.user_rent_status_recyclerView)
    }

    private fun datePickerManager() {
        sublimePickerDialogFragment.show(fragmentManager,null)
    }

    private fun datePickerSetting() {
        userRentStartDatePickerImageView!!.setOnClickListener {
            datePickerManager()
            //var bundle = Bundle()
            //            //sublimePickerDialogFragment.arguments = bundle
/*
            val fragmentManager = supportFragmentManager
            var sublimePickerDialogFragment = SublimePickerDialogFragment()
            var bundle = Bundle()
            // put arguments into bundle
            sublimePickerDialogFragment.arguments = bundle
            sublimePickerDialogFragment.isCancelable = false
            sublimePickerDialogFragment.show(fragmentManager,null)
 */
        }
    }

    private fun rentAddButtonSetting() {
        userRentAddButton!!.setOnClickListener {
            if (userRentProductListSpinnerHashMap[productListDataArrayListIndex]!!.isEmpty())
                Snackbar.make(it, getString(R.string.dont_have_rent_available), Snackbar.LENGTH_LONG).show()
            else if (startDateEdit!!.length() == 0){
                Snackbar.make(it, getString(R.string.plz_pick_rent_start_date), Snackbar.LENGTH_LONG).show()
            }
            else {
                // 대여 시작
                showProgressDialog(getString(R.string.rent_adding))
                rendAddManager = RentAddManager()
                rendAddManager!!.execute(session, "0", "1", "1", userRentProductListData!!.productIndex.toString(), startDateEdit!!.text.toString(), "rentIndex", userRentProductListData!!.productName)
            }
        }
    }

    private fun setSpinnerListener() {
        userRentGroupSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                productListDataArrayListIndex = position
                rentAblePeriod = userRentGroupSpinnerArrayList[position].groupRentable!!.toLong()

                val userRentProductListSpinnerAdapter = ArrayAdapter(this@MainActivity_User, android.R.layout.simple_spinner_dropdown_item, userRentProductListSpinnerHashMap[position])
                userRentProductListSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                userRentProductListSpinner!!.adapter = userRentProductListSpinnerAdapter
                userRentProductListSpinner!!.isEnabled = true
            }
            override fun onNothingSelected(parent: AdapterView<*>?) {

            }
        }
        userRentProductListSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                userRentProductListData = userRentProductListSpinnerHashMap[productListDataArrayListIndex!!]!![position]
                startDateEdit!!.setText("")
                finishDateEdit!!.setText("")
            }
            override fun onNothingSelected(parent: AdapterView<*>?) {

            }
        }
    }

    private fun rentAddViewSetting() {
        userRentGroupSpinner = findViewById(R.id.user_rent_group_spinner)
        userRentProductListSpinner = findViewById(R.id.user_rent_product_list_spinner)
        userRentStartDatePickerImageView = findViewById(R.id.user_rent_start_date_picker_imageView)
        userRentStartDateTextInputLayout = findViewById(R.id.user_rent_start_date_textInputLayout)
        startDateEdit = findViewById(R.id.start_date_edit)
        finishDateEdit = findViewById(R.id.finish_date_edit)
        userRentAddButton = findViewById(R.id.user_rent_add_button)
    }

    private inner class RentListAndProductListManager : HttpPostSendRentListAndProductList() {
        // string to json

        fun getRentListJSONData(): JSONObject { return JSONObject(rentListAllData) }
        fun getRentListData(): String? { return rentListAllData  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("UserRentList", getRentListData())

            if (result == 1) {
                if (getRentListJSONData()["result"] == 0) {
                    if (userRentListArrayList.isNotEmpty())
                        userRentListArrayList.clear()

                    if (userRentStatusRecyclerView!!.adapter != null)
                        userRentStatusRecyclerView!!.adapter!!.notifyDataSetChanged()

                    for (i in 0 until JSONArray(getRentListJSONData()["rents"].toString()).length())
                        userRentListArrayList.add(UserRentStatusData(JSONObject((JSONArray(getRentListJSONData()["rents"].toString())[i]).toString())))

                    val adapter = UserRentStatusRecyclerViewAdapter(this@MainActivity_User, userRentListArrayList)
                    userRentStatusRecyclerView!!.layoutManager = LinearLayoutManager(this@MainActivity_User)
                    userRentStatusRecyclerView!!.adapter = adapter

                    val userRentGroupSpinnerAdapter = ArrayAdapter(this@MainActivity_User, android.R.layout.simple_spinner_dropdown_item, userRentGroupSpinnerArrayList)
                    userRentGroupSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    userRentGroupSpinner!!.adapter = userRentGroupSpinnerAdapter

                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_User, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getRentListJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.dont_have_permission_error)
                        -4 -> builder.setMessage(R.string.searching_error)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }
            }
            else {
                val builder = AlertDialog.Builder(this@MainActivity_User, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.loading_data_fail)
                builder.setMessage(R.string.loading_data_fail_message)
                //if ("session") // session 로그인
                //    builder.setMessage(R.string.expire_session_error)
                builder.setPositiveButton(android.R.string.ok, null)
                builder.show()
            }
            return
        }
    }

    abstract inner class HttpPostSendRentListAndProductList : AsyncTask<String, Int, Int>() {

        private val rentListApiName = "rent_list.php"
        private val productListApiName = "product_list.php"

        protected var rentListAllData: String? = null
        protected var productListAllData: String? = null

        abstract fun onPreTask()
        abstract fun onUpdate(prgInt: Int)
        abstract fun onFinish(result: Int)

        override fun onPreExecute() {
            //super.onPreExecute()
            onPreTask()
        }

        override fun doInBackground(vararg params: String): Int {
            // params[0] == session
            // params[1] == defaultUseCaches
            // params[2] == doInput
            // params[3] == doOutput
            // params[4] == user index

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true

            publishProgress(10)

            try {
                val rentListAPI = BackEndAPI_RentSystemRentList(rentListApiName, defaultUseCaches, doInput, doOutput)
                rentListAllData = rentListAPI.httpsPostSend_RentList(params[0], params[4], "rentUser")

                publishProgress(30)

                val productListAPI = BackEndAPI_ProductSystemProductList(productListApiName, defaultUseCaches, doInput, doOutput)
                productListAllData = productListAPI.httpsPostSend_ProductList(params[0])

                publishProgress(45)

                if (userRentGroupSpinnerArrayList.isNotEmpty())
                    userRentGroupSpinnerArrayList.clear()

                if (userRentProductListSpinnerHashMap.isNotEmpty())
                    userRentProductListSpinnerHashMap.clear()

                val groupListLength = JSONArray(JSONObject(productListAllData).get("groups").toString()).length()
                for (i in 0 until groupListLength)
                    userRentGroupSpinnerArrayList.add(GroupListData(JSONObject((JSONArray(JSONObject(productListAllData).get("groups").toString())[i]).toString())))

                publishProgress(60)

                val productListLength = JSONArray(JSONObject(productListAllData).get("products").toString()).length()
                for (i in 0 until productListLength)
                    userRentProductListSpinnerHashMap[i] = ArrayList()

                var productListJSONArray = JSONArray(JSONObject(productListAllData).get("products").toString())

                Log.d("len", productListLength.toString())
                for (i in 0 until productListLength) {
                    if (i == productListLength / 3)
                        publishProgress(75)
                    if (i == (productListLength / 3) * 2)
                        publishProgress(90)
                    Log.d("test", (JSONArray(JSONObject(productListAllData).get("products").toString())[i]).toString())
                    if ((JSONObject(productListJSONArray[i].toString())).get("product_group_index").toString().toInt() > 0)
                        userRentProductListSpinnerHashMap[(JSONObject(productListJSONArray[i].toString())).get("product_group_index").toString().toInt() - 1]!!.add(ProductListData(JSONObject((JSONArray(JSONObject(productListAllData).get("products").toString())[i]).toString())))
                }

                publishProgress(100)
                /*
                val productListLength = JSONArray(JSONObject(productListAllData).get("products").toString()).length()
                for (i in 0 until productListLength)
                    userRentProductListSpinnerArrayList.add(ProductListData(JSONObject((JSONArray(JSONObject(productListAllData).get("products").toString())[i]).toString())))

                val userRentGroupSpinnerAdapter = ArrayAdapter(this@MainActivity_User, android.R.layout.simple_spinner_dropdown_item, userRentGroupSpinnerArrayList)
                userRentGroupSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                userRentGroupSpinner!!.adapter = userRentGroupSpinnerAdapter

                val userRentProductListSpinnerAdapter = ArrayAdapter(this@MainActivity_User, android.R.layout.simple_spinner_dropdown_item, userRentProductListSpinnerArrayList)
                userRentProductListSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                userRentProductListSpinner!!.adapter = userRentProductListSpinnerAdapter
                 */
            }
            catch (e: Exception) {
                Log.e("RentList Error", "Message : " + e.message)
                Log.e("RentList Error", "LocalizedMessage : " + e.localizedMessage)
                return 0
            }
            return 1
        }

        override fun onProgressUpdate(vararg values: Int?) {
            //super.onProgressUpdate(*values)
            onUpdate(values[0]!!)
        }

        override fun onPostExecute(result: Int?) {
            super.onPostExecute(result)
            onFinish(result!!)
        }
    }

    private inner class RentAddManager : HttpPostSendRentAdd() {
        // string to json

        fun getRentAddJSONData(): JSONObject { return JSONObject(rentAddAllData) }
        fun getRentAddData(): String? { return rentAddAllData  }
        fun getRentProductNameData(): String? { return rentProductName  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("RentAdd", getRentAddData())

            if (result == 1) {
                if (getRentAddJSONData()["result"] == 0) {
                    val builder = AlertDialog.Builder(this@MainActivity_User, R.style.MyAlertDialogStyle)
                    builder.setTitle(R.string.rent_add_success)

                    val rentAddResult = StringBuffer()
                    rentAddResult.append(getRentProductNameData()).append(" 의 대여 신청이 완료되었습니다 :)").append("\n")
                    rentAddResult.append("허가 될 때까지 기다려주세요 :)")

                    builder.setMessage(rentAddResult)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_User, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getRentAddJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.dont_have_permission_error)
                        -4 -> builder.setMessage(R.string.searching_error)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }
            }
            else {
                val builder = AlertDialog.Builder(this@MainActivity_User, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.loading_data_fail)
                builder.setMessage(R.string.loading_data_fail_message)
                //if ("session") // session 로그인
                //    builder.setMessage(R.string.expire_session_error)
                builder.setPositiveButton(android.R.string.ok, null)
                builder.show()
            }
            return
        }
    }

    abstract inner class HttpPostSendRentAdd : AsyncTask<String, Int, Int>() {

        private val rentAddApiName = "rent_add.php"
        protected var rentProductName : String? = null

        protected var rentAddAllData: String? = null

        abstract fun onPreTask()
        abstract fun onUpdate(prgInt: Int)
        abstract fun onFinish(result: Int)

        override fun onPreExecute() {
            //super.onPreExecute()
            onPreTask()
        }

        override fun doInBackground(vararg params: String): Int {
            // params[0] == session
            // params[1] == defaultUseCaches
            // params[2] == doInput
            // params[3] == doOutput
            // params[4] == rent_index or barcode
            // params[5] == rent_time_start
            // params[6] == rent(product)Index or productBarcode
            // params[7] == 물품 이름

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true
            rentProductName = params[7]

            publishProgress(10)

            try {
                val rentAddAPI = BackEndAPI_RentSystemRentAdd(rentAddApiName, defaultUseCaches, doInput, doOutput)

                publishProgress(70)

                rentAddAllData = rentAddAPI.httpsPostSend_RentAdd(params[0], params[4], params[5] + " 00:00:00", params[6])

                publishProgress(100)
                /*
                val productListLength = JSONArray(JSONObject(productListAllData).get("products").toString()).length()
                for (i in 0 until productListLength)
                    userRentProductListSpinnerArrayList.add(ProductListData(JSONObject((JSONArray(JSONObject(productListAllData).get("products").toString())[i]).toString())))

                val userRentGroupSpinnerAdapter = ArrayAdapter(this@MainActivity_User, android.R.layout.simple_spinner_dropdown_item, userRentGroupSpinnerArrayList)
                userRentGroupSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                userRentGroupSpinner!!.adapter = userRentGroupSpinnerAdapter

                val userRentProductListSpinnerAdapter = ArrayAdapter(this@MainActivity_User, android.R.layout.simple_spinner_dropdown_item, userRentProductListSpinnerArrayList)
                userRentProductListSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                userRentProductListSpinner!!.adapter = userRentProductListSpinnerAdapter
                 */
            }
            catch (e: Exception) {
                Log.e("RentList Error", "Message : " + e.message)
                Log.e("RentList Error", "LocalizedMessage : " + e.localizedMessage)
                return 0
            }
            return 1
        }

        override fun onProgressUpdate(vararg values: Int?) {
            //super.onProgressUpdate(*values)
            onUpdate(values[0]!!)
        }

        override fun onPostExecute(result: Int?) {
            super.onPostExecute(result)
            onFinish(result!!)
        }
    }

    private inner class LogOutManager : HttpPostSendLogOut() {
        fun getLogOutJSONData(): JSONObject { return JSONObject(allData) }
        fun getLogOutData(): String? { return allData  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("LogOut", getLogOutData())

            if (result == 1) {
                if (getLogOutJSONData()["result"] == 0) {
                    Toast.makeText(this@MainActivity_User, getString(R.string.success_logout), Toast.LENGTH_SHORT).show()
                    startActivity(Intent(applicationContext, LoginActivity::class.java))
                    this@MainActivity_User.finish()
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_User, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getLogOutJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.failed_logout)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }
            }
            else {
                val builder = AlertDialog.Builder(this@MainActivity_User, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.logout_fail)
                builder.setMessage(R.string.failed_logout)
                //if ("session") // session 로그인
                //    builder.setMessage(R.string.expire_session_error)
                builder.setPositiveButton(android.R.string.ok, null)
                builder.show()
            }
            return
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        try {
            if (rentListAndProductListManager!!.status == AsyncTask.Status.RUNNING)
                rentListAndProductListManager!!.cancel(true)
            if (rendAddManager!!.status == AsyncTask.Status.RUNNING)
                rendAddManager!!.cancel(true)
        } catch (e: Exception) {
            e.printStackTrace()
            Log.e("UserRentListManager", "Message : " + e.message)
        }
    }

    override fun onBackPressed() {
        if (drawer!!.isDrawerOpen(GravityCompat.START))
            drawer!!.closeDrawer(GravityCompat.START)
        else {
            if (System.currentTimeMillis() > backKeyClickTime + 2000) {
                backKeyClickTime = System.currentTimeMillis()
                Toast.makeText(
                    applicationContext,
                    "뒤로 가기 버튼을 한 번 더 누르면 종료됩니다 :) ",
                    Toast.LENGTH_SHORT
                ).show()
                return
            }
            if (System.currentTimeMillis() <= backKeyClickTime + 2000)
                finish()
        }
    }

    private class GroupListData(obj : JSONObject) {
        var groupIndex : Int? = null
        var groupName : String? = null
        var groupRentable : Int? = null

        init {
            groupIndex = obj.get("group_index").toString().toInt()
            groupName = obj.get("group_name").toString()
            groupRentable = obj.get("group_rentable").toString().toInt()
        }

        override fun toString(): String {
            if (groupName == null)
                return ""
            return groupName!!
        }
    }

    private class ProductListData(obj : JSONObject) {
        var productName : String? = null
        var productBarcode : String? = null
        var productIndex : Int? = null

        init {
            productName  = obj.get(productNameStr).toString()
            productBarcode  = obj.get(productBarcodeStr).toString()
            productIndex  = obj.get(productIndexStr).toString().toInt()
        }

        override fun toString() : String {
            return productName!!
        }
    }

}