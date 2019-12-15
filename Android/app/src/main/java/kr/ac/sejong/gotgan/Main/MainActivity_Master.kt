package kr.ac.sejong.gotgan.Main

import android.app.ProgressDialog
import android.content.Intent
import android.graphics.Color
import android.os.AsyncTask
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.util.Log
import android.view.MenuItem
import android.view.View
import android.widget.*
import androidx.appcompat.app.ActionBar
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.widget.AppCompatSpinner
import androidx.appcompat.widget.AppCompatTextView
import androidx.appcompat.widget.Toolbar
import androidx.cardview.widget.CardView
import androidx.core.view.GravityCompat
import androidx.drawerlayout.widget.DrawerLayout
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.github.mikephil.charting.animation.Easing
import com.github.mikephil.charting.charts.PieChart
import com.github.mikephil.charting.components.Legend
import com.github.mikephil.charting.data.PieData
import com.github.mikephil.charting.data.PieDataSet
import com.github.mikephil.charting.data.PieEntry
import com.google.android.material.button.MaterialButton
import com.google.android.material.navigation.NavigationView
import com.google.android.material.snackbar.Snackbar
import com.google.android.material.tabs.TabLayout
import com.google.android.material.textfield.TextInputEditText
import com.google.zxing.integration.android.IntentIntegrator
import com.google.zxing.integration.android.IntentResult
import kr.ac.sejong.gotgan.BackEndAPI.*
import kr.ac.sejong.gotgan.BackEndAPI.ProductSystem.BackEndAPI_ProductSystemProductAdd
import kr.ac.sejong.gotgan.BackEndAPI.ProductSystem.BackEndAPI_ProductSystemProductGroupAdd
import kr.ac.sejong.gotgan.BackEndAPI.ProductSystem.BackEndAPI_ProductSystemProductList
import kr.ac.sejong.gotgan.BackEndAPI.ProductSystem.BackEndAPI_ProductSystemProductOverview
import kr.ac.sejong.gotgan.BackEndAPI.RentSystem.BackEndAPI_RentSystemRentList
import kr.ac.sejong.gotgan.HttpPostSendLogOut
import kr.ac.sejong.gotgan.Login.LoginActivity
import kr.ac.sejong.gotgan.R
import kr.ac.sejong.gotgan.ZXingActivity
import org.json.JSONArray
import org.json.JSONObject
import kotlin.system.exitProcess

class MainActivity_Master : AppCompatActivity(), NavigationView.OnNavigationItemSelectedListener {

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

    companion object {
        var rentListArrayList: ArrayList<MasterHomeInOutData> = ArrayList()
        var inOutManagementRecyclerView: RecyclerView? = null
        var inOutManagementRecyclerViewInsteadTextView: AppCompatTextView? = null
        //var barcodeSearch : ProductManagementSearchWithBarcode? = null

        var rentStatusArrayList : ArrayList<MasterInOutManagementData> = ArrayList()
        var rentStatusRecyclerView : RecyclerView? = null
        var rentStatusRecyclerViewInsteadTextView : AppCompatTextView? = null

        var rentedStatusArrayList : ArrayList<MasterInOutManagementData> = ArrayList()
        var rentedStatusRecyclerView : RecyclerView? = null
        var rentedStatusRecyclerViewInsteadTextView : AppCompatTextView? = null
    }

    //private var rentStatusArrayList : ArrayList<MasterInOutManagementData> = ArrayList()
    //private var rentedStatusArrayList : ArrayList<MasterInOutManagementData> = ArrayList()

    private var logoutManager : LogOutManager? = null

    private var toolbar : Toolbar? = null
    private var drawer : DrawerLayout? = null
    private var navigationView : NavigationView? = null
    private var actionBar : ActionBar? = null
    private var userNameTextView : TextView? = null
    private var userLevelTextView : TextView? = null
    private var userName : String? = null
    private var userLevel : String? = null

    private var rentListManager : RentListManager? = null

    private var backKeyClickTime : Long = 0

    private var productListArrayList : ArrayList<MasterProductListData> = ArrayList()
    private var groupListHashMap : HashMap<Int, ArrayList<MasterProductListData>> = HashMap()

    private var barcodeSearch : ProductManagementSearchWithBarcode? = null

    private var progressDialog : ProgressDialog? = null
    private var session : String? = null
    private var productOverViewManager : ProductOverViewAndRentListManager? = null
    private var productOverViewProductManagementManager : ProductOverViewManager? = null
    private var stockSpinner : AppCompatSpinner? = null
    private var stockPieChart : PieChart? = null
    private var productOverViewArrayList : ArrayList<ProductOverViewGroupData> = ArrayList()
    private var productOverViewProductManagementArrayList : ArrayList<ProductOverViewGroupData> = ArrayList()
    //private var rentListArrayList : ArrayList<MasterHomeInOutData> = ArrayList()

    private var stockCountTextView: AppCompatTextView? = null
    private var lendCountTextView: AppCompatTextView? = null
    private var keepCountTextView: AppCompatTextView? = null

    //private var inOutManagementRecyclerView : RecyclerView? = null
    //private var inOutManagementRecyclerViewInsteadTextView : AppCompatTextView? = null

    private var masterHomeLayout : View? = null
    private var masterProductListLayout : View? = null
    private var masterProductManagementLayout : View? = null
    private var masterInoutManagementLayout : View? = null

    //private var stockPieChart : PieChart? = null
    private var masterTabLayout : TabLayout? = null

    private var productListRecyclerView : RecyclerView? = null
    private var productListManager : ProductListManager? = null
    private var emptyProductListTextView : AppCompatTextView? = null

    // productManagementViews
    private var productManagementBarcode : CardView? = null

    private var productManagementProductNameEdit : TextInputEditText? = null
    private var productManagementProductGroupNameSpinner : AppCompatSpinner? = null
    private var productManagementProductStatusSpinner : AppCompatSpinner? = null
    private var productManagementProductDepartmentSpinner : AppCompatSpinner? = null
    private var productManagementProductBarcodeEdit : TextInputEditText? = null
    private var productManagementProductAddButton : MaterialButton? = null

    private var productManagementGroupAddEdit : TextInputEditText? = null
    private var productManagementGroupRentIsAvailableSpinner : AppCompatSpinner? = null

    private var productManagementGroupRentDateEdit : TextInputEditText? = null
    private var productManagementGroupImportanceSpinner : AppCompatSpinner? = null
    private var productManagementGroupAddButton : MaterialButton? = null

    private var productManagementProductGroupIndex : Int? = null
    private var productManagementProductStatus : Int? = null
    private var productManagementProductOwner : Int? = null

    private var productManagementProductRentable : Int? = null
    private var productManagementProductPriority : Int? = null

    private var productManagementProductAddManager : ProductManagementProductAddManager? = null
    private var productManagementGroupAddManager : ProductManagementGroupAddManager? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main_master_drawer)

        session = JSONObject(intent.getStringExtra("userAllData").toString())["session"].toString()

        userName = JSONObject(intent.getStringExtra("userAllData").toString())["user_name"].toString()
        when (JSONObject(intent.getStringExtra("userAllData").toString())["user_level"].toString().toInt()) {
            0 -> userLevel = "일반 사용자"
            1 -> userLevel = "관리자"
            2 -> userLevel = "최고 관리자"
        }

        allLayoutSetting()
        // Master Home
        homeViewIdSetting()
        settingSpinner()
        pieChartBaseSetting()

        // etcViewIdSetting()
        productManagementIdSetting()
        productManagementIdSetting()

        rentStatusSetting()

        tabLayoutSetting()

        session = JSONObject(intent.getStringExtra("userAllData").toString())["session"].toString() /*String형*/
        showProgressDialog(getString(R.string.loading_data))
        productOverViewManager = ProductOverViewAndRentListManager()
        productOverViewManager!!.execute(session, "1", "1", "1")
        //ProductListManager().execute(session, "1", "1", "1")
    }

    private fun showProgressDialogVertical(title : String, message : String) {
        progressDialog = ProgressDialog(this)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_SPINNER)
        progressDialog!!.setTitle(title)
        progressDialog!!.setMessage(message)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    private fun allLayoutSetting() {
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

        masterHomeLayout = findViewById(R.id.main_master_home_layout)
        masterHomeLayout!!.visibility = View.VISIBLE
        masterProductListLayout = findViewById(R.id.main_master_product_list_layout)
        masterProductListLayout!!.visibility = View.GONE
        masterProductManagementLayout = findViewById(R.id.main_master_product_management_layout)
        masterProductManagementLayout!!.visibility = View.GONE
        masterInoutManagementLayout = findViewById(R.id.main_master_inout_management_layout)
        masterInoutManagementLayout!!.visibility = View.GONE
        //masterLogLayout = findViewById(R.id.main_master_log_layout)
        //masterLogLayout!!.visibility = View.GONE
    }

    private fun homeViewIdSetting() {
        stockCountTextView = findViewById(R.id.stock_count_textView)
        lendCountTextView = findViewById(R.id.lend_count_textView)
        keepCountTextView = findViewById(R.id.keep_count_textView)
        stockPieChart = findViewById(R.id.stock_pieChart)
        inOutManagementRecyclerView = findViewById(R.id.import_export_management_recyclerView)
        inOutManagementRecyclerViewInsteadTextView = findViewById(R.id.import_export_management_recyclerView_instead_textView)
    }

    private fun rentStatusSetting() {
        rentStatusRecyclerView = findViewById(R.id.inout_management_rent_status_recyclerView)
        rentStatusRecyclerViewInsteadTextView = findViewById(R.id.inout_management_rent_status_no_data_textView)
        rentStatusRecyclerViewInsteadTextView!!.visibility = View.GONE

        rentedStatusRecyclerView = findViewById(R.id.inout_management_rented_status_recyclerView)
        rentedStatusRecyclerViewInsteadTextView = findViewById(R.id.inout_management_rented_status_no_data_textView)
        rentedStatusRecyclerViewInsteadTextView!!.visibility = View.GONE
    }

    private fun productListIdSetting() {
        productListRecyclerView = findViewById(R.id.productListRecyclerView)
        emptyProductListTextView = findViewById(R.id.empty_product_list_textView)
    }

    private fun productManagementIdSetting() {
        productManagementBarcode = findViewById(R.id.barcode_CardView)

        productManagementProductNameEdit = findViewById(R.id.product_name_edit)
        productManagementProductGroupNameSpinner = findViewById(R.id.product_group_name_spinner)
        productManagementProductStatusSpinner = findViewById(R.id.product_status_spinner)
        productManagementProductDepartmentSpinner = findViewById(R.id.product_department_spinner)
        productManagementProductBarcodeEdit = findViewById(R.id.product_barcode_edit)
        productManagementProductAddButton = findViewById(R.id.product_add_button)

        productManagementGroupAddEdit = findViewById(R.id.group_add_edit)
        productManagementGroupRentIsAvailableSpinner = findViewById(R.id.group_rent_is_available_spinner)
        productManagementGroupRentDateEdit = findViewById(R.id.group_rent_date_edit)
        productManagementGroupImportanceSpinner = findViewById(R.id.group_importance_spinner)
        productManagementGroupAddButton = findViewById(R.id.group_add_button)
    }

    private fun productManagementBarcodeSetting() {
        productManagementBarcode!!.setOnClickListener {
            var integrator = IntentIntegrator(this)
            integrator.captureActivity = ZXingActivity::class.java
            integrator.setOrientationLocked(false)
            integrator.initiateScan()
        }
    }

    private fun productManagementSpinnerSetting() {

        val productManagementProductStatusSpinnerAdapter = ArrayAdapter(this@MainActivity_Master, android.R.layout.simple_spinner_dropdown_item, resources.getStringArray(R.array.product_add_status_array))
        productManagementProductStatusSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        productManagementProductStatusSpinner!!.adapter = productManagementProductStatusSpinnerAdapter

        val productManagementProductDepartmentSpinnerAdapter = ArrayAdapter(this@MainActivity_Master, android.R.layout.simple_spinner_dropdown_item, resources.getStringArray(R.array.product_add_department_array))
        productManagementProductDepartmentSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        productManagementProductDepartmentSpinner!!.adapter = productManagementProductDepartmentSpinnerAdapter

        productManagementProductGroupIndex = 1
        productManagementProductStatus = 0
        productManagementProductOwner = 0
        productManagementProductRentable = 0
        productManagementProductPriority = 0

        productManagementProductGroupNameSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                productManagementProductGroupIndex = position + 1
            }
            override fun onNothingSelected(parent: AdapterView<*>?) { }
        }

        productManagementProductStatusSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                productManagementProductStatus = position
            }
            override fun onNothingSelected(parent: AdapterView<*>?) { }
        }

        productManagementProductDepartmentSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                productManagementProductOwner = position
            }
            override fun onNothingSelected(parent: AdapterView<*>?) { }
        }


        val productManagementGroupRentIsAvailableSpinnerAdapter = ArrayAdapter(this@MainActivity_Master, android.R.layout.simple_spinner_dropdown_item, resources.getStringArray(R.array.product_group_rentable_array))
        productManagementGroupRentIsAvailableSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        productManagementGroupRentIsAvailableSpinner!!.adapter = productManagementGroupRentIsAvailableSpinnerAdapter

        val productManagementGroupImportanceSpinnerAdapter = ArrayAdapter(this@MainActivity_Master, android.R.layout.simple_spinner_dropdown_item, resources.getStringArray(R.array.product_group_importance_array))
        productManagementGroupImportanceSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        productManagementGroupImportanceSpinner!!.adapter = productManagementGroupImportanceSpinnerAdapter

        productManagementGroupRentIsAvailableSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                when (productManagementGroupRentIsAvailableSpinner!!.getItemAtPosition(position).toString()) {
                    "불가" -> {
                        productManagementGroupRentDateEdit!!.setText("")
                        productManagementGroupRentDateEdit!!.isEnabled = false
                        productManagementProductRentable = 0
                    }
                    "가능" -> {
                        productManagementGroupRentDateEdit!!.isEnabled = true
                        if (productManagementGroupRentDateEdit!!.length() != 0)
                            productManagementProductRentable = productManagementGroupRentDateEdit!!.text.toString().toInt()
                    }
                }
            }
            override fun onNothingSelected(parent: AdapterView<*>?) { }
        }

        productManagementGroupImportanceSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                productManagementProductPriority = position
            }
            override fun onNothingSelected(parent: AdapterView<*>?) { }
        }

    }

    private fun productManagementProductAddButtonSetting() {
        productManagementProductAddButton!!.setOnClickListener {
            if (productManagementProductNameEdit!!.length() == 0)
                Snackbar.make(it, getString(R.string.plz_input_product_name), Snackbar.LENGTH_SHORT).show()
            else {
                var productAddJSONObject = JSONObject()
                productAddJSONObject.put(productGroupStr, productManagementProductGroupIndex) // spinner
                productAddJSONObject.put(productNameStr, productManagementProductNameEdit!!.text.toString())
                productAddJSONObject.put(productStatusStr, productManagementProductStatus) // spinner
                productAddJSONObject.put(productOwnerStr, productManagementProductOwner) // spinner department
                if (productManagementProductBarcodeEdit!!.length() != 0)
                    productAddJSONObject.put(productBarcodeStr, productManagementProductBarcodeEdit!!.text.toString().toInt()) // edit
                else
                    productAddJSONObject.put(productBarcodeStr, 0) // edit

                var productAddJSONArray = JSONArray()
                productAddJSONArray.put(productAddJSONObject)

                session = JSONObject(intent.getStringExtra("userAllData").toString())["session"].toString()
                productManagementProductAdd(session!!, productAddJSONArray, productManagementProductNameEdit!!.text.toString())
            }
        }
    }

    private fun productManagementProductGroupAddButtonSetting() {
        productManagementGroupAddButton!!.setOnClickListener {
            if (productManagementGroupAddEdit!!.length() == 0)
                Snackbar.make(it, getString(R.string.plz_input_product_name), Snackbar.LENGTH_SHORT).show()
            else {
                session = JSONObject(intent.getStringExtra("userAllData").toString())["session"].toString()
                if (!productManagementGroupRentDateEdit!!.isEnabled) {
                    productManagementProductRentable = 0
                    productManagementGroupAdd(session!!, productManagementGroupAddEdit!!.text.toString(), productManagementProductRentable!!, productManagementProductPriority!!)
                }
                else { // isEnable == true
                    if (productManagementGroupRentDateEdit!!.length() == 0) {
                        productManagementProductRentable = 0
                        Snackbar.make(it, getString(R.string.plz_input_rentable_date_name), Snackbar.LENGTH_SHORT).show()
                    }
                    else {
                        productManagementProductRentable = productManagementGroupRentDateEdit!!.text.toString().toInt()
                        productManagementGroupAdd(session!!, productManagementGroupAddEdit!!.text.toString(), productManagementProductRentable!!, productManagementProductPriority!!)
                    }
                }
            }
        }
    }

    private fun productManagementProductAdd(session : String, addJSONArrayData : JSONArray, productName : String) {
        showProgressDialog(getString(R.string.loading_data))
        productManagementProductAddManager = ProductManagementProductAddManager()
        productManagementProductAddManager!!.execute(session, "1", "1", "1", addJSONArrayData.toString(), productName)
        // params[4] == products : JSONArray
        // params[5] == product name
    }

    private fun productManagementGroupAdd(session : String, productGroupName : String, productGroupRentable : Int, productGroupPriority : Int) {
        showProgressDialog(getString(R.string.loading_data))
        productManagementGroupAddManager = ProductManagementGroupAddManager()
        productManagementGroupAddManager!!.execute(session, "1", "1", "1", productGroupName, productGroupRentable.toString(), productGroupPriority.toString())
        // params[4] == product_group_name
        // params[5] == product_group_rentable
        // params[6] == product_group_priority
    }

    private fun pieChartBaseSetting() {
        stockPieChart!!.setUsePercentValues(false) // 퍼센트 값으로 설정
        stockPieChart!!.isDragDecelerationEnabled = false // 돌리기 방지
        stockPieChart!!.isRotationEnabled = false
        stockPieChart!!.description.isEnabled = false // 차트 설명 enable
        stockPieChart!!.setExtraOffsets(5f,10f,5f,5f)
        stockPieChart!!.setDrawCenterText(true)

        stockPieChart!!.dragDecelerationFrictionCoef = 0.95f

        stockPieChart!!.isDrawHoleEnabled = true
        stockPieChart!!.setHoleColor(Color.WHITE)
        stockPieChart!!.transparentCircleRadius = 61f
    }

    private fun pieChartRenew(productOverView : ProductOverViewGroupData) {

        val pieValues = ArrayList<PieEntry>()

        if (productOverView.groupCountAvailable!! > 0)
            pieValues.add(PieEntry((productOverView.groupCountAvailable!! - productOverView.groupCountRent!!).toFloat(), getString(R.string.available_count)))
        if (productOverView.groupCountUnavailable!! > 0)
            pieValues.add(PieEntry(productOverView.groupCountUnavailable!!.toFloat(),getString(R.string.unavailable_count)))
        if (productOverView.groupCountBroken!! > 0)
            pieValues.add(PieEntry(productOverView.groupCountBroken!!.toFloat(),getString(R.string.broken_count)))
        if (productOverView.groupCountRepair!! > 0)
            pieValues.add(PieEntry(productOverView.groupCountRepair!!.toFloat(),getString(R.string.repair_count)))
        if (productOverView.groupCountRent!! > 0)
            pieValues.add(PieEntry(productOverView.groupCountRent!!.toFloat(),getString(R.string.rent_count)))

        //var description = Description()
        //description.text = productOverView.groupName
        //description.textSize = 15f

        stockPieChart!!.animateY(1000, Easing.EaseInOutCubic)
        if (pieValues.isEmpty())
            stockPieChart!!.centerText = productOverView.groupName + "의 모든 재고가 없습니다 :("
        else
            stockPieChart!!.centerText = productOverView.groupName

        var legend : Legend = stockPieChart!!.legend
        legend.verticalAlignment = Legend.LegendVerticalAlignment.BOTTOM
        legend.horizontalAlignment = Legend.LegendHorizontalAlignment.CENTER
        legend.orientation = Legend.LegendOrientation.HORIZONTAL
        legend.setDrawInside(false)
        legend.xEntrySpace = 7f
        legend.yEntrySpace = 0f
        legend.yOffset = 7f

        var dataSet = PieDataSet(pieValues, null)
        dataSet.sliceSpace = 3f
        dataSet.selectionShift = 5f

        var colorArray = arrayOf(
            Color.rgb(255, 152, 0),
            Color.rgb(76, 175, 80),
            Color.rgb(244, 67, 54),
            Color.rgb(0, 188, 212),
            Color.rgb(0, 150, 136)
        )

        val colors = ArrayList<Int>()
        for (c in colorArray) // ColorTemplate.JOYFUL_COLORS
            colors.add(c)

        dataSet.colors = colors

        var data = PieData(dataSet)
        data.setValueTextSize(10f)
        data.setValueTextColor(Color.YELLOW)

        stockPieChart!!.data = data
    }

    private fun tabLayoutSetting() {
        masterTabLayout = findViewById(R.id.main_master_tabLayout)

        val customTabViewHome : View = layoutInflater.inflate(R.layout.custom_tab, null)
        val iconImgHome : ImageView = customTabViewHome.findViewById(R.id.img_tab)
        val iconTextHome : TextView = customTabViewHome.findViewById(R.id.txt_tab)
        iconImgHome.setImageResource(R.drawable.home_icon)
        iconTextHome.setText(R.string.home)
        masterTabLayout!!.addTab(masterTabLayout!!.newTab().setCustomView(customTabViewHome))

        val customTabViewProductList : View = layoutInflater.inflate(R.layout.custom_tab, null)
        val iconImgProductList : ImageView = customTabViewProductList.findViewById(R.id.img_tab)
        val iconTextProductList : TextView = customTabViewProductList.findViewById(R.id.txt_tab)
        iconImgProductList.setImageResource(R.drawable.product_list_icon)
        iconTextProductList.setText(R.string.product_list)
        masterTabLayout!!.addTab(masterTabLayout!!.newTab().setCustomView(customTabViewProductList))

        val customTabViewProductManagement : View = layoutInflater.inflate(R.layout.custom_tab, null)
        val iconImgProductManagement : ImageView = customTabViewProductManagement.findViewById(R.id.img_tab)
        val iconTextProductManagement : TextView = customTabViewProductManagement.findViewById(R.id.txt_tab)
        iconImgProductManagement.setImageResource(R.drawable.product_management_icon)
        iconTextProductManagement.setText(R.string.product_management)
        masterTabLayout!!.addTab(masterTabLayout!!.newTab().setCustomView(customTabViewProductManagement))

        val customTabViewInOutManagement : View = layoutInflater.inflate(R.layout.custom_tab, null)
        val iconImgInOutManagement : ImageView = customTabViewInOutManagement.findViewById(R.id.img_tab)
        val iconTextInOutManagement : TextView = customTabViewInOutManagement.findViewById(R.id.txt_tab)
        iconImgInOutManagement.setImageResource(R.drawable.inout_management_icon)
        iconTextInOutManagement.setText(R.string.inout_management)
        masterTabLayout!!.addTab(masterTabLayout!!.newTab().setCustomView(customTabViewInOutManagement))
/*
        val customTabViewLog : View = layoutInflater.inflate(R.layout.custom_tab, null)
        val iconImgLog : ImageView = customTabViewLog.findViewById(R.id.img_tab)
        val iconTextInOutLog : TextView = customTabViewLog.findViewById(R.id.txt_tab)
        iconImgLog.setImageResource(R.drawable.log_icon)
        iconTextInOutLog.setText(R.string.log)
        masterTabLayout!!.addTab(masterTabLayout!!.newTab().setCustomView(customTabViewLog))

 */

        masterTabLayout!!.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab?) { layoutVisibilityManager(tab!!.position)}
            override fun onTabReselected(tab: TabLayout.Tab?) { }
            override fun onTabUnselected(tab: TabLayout.Tab?) { }
        })
    }

    private fun layoutVisibilityManager(position : Int) {
        when (position) {
            0 -> {
                masterHomeLayout!!.visibility = View.VISIBLE
                masterProductListLayout!!.visibility = View.GONE
                masterProductManagementLayout!!.visibility = View.GONE
                masterInoutManagementLayout!!.visibility = View.GONE
                //masterLogLayout!!.visibility = View.GONE
                session = JSONObject(intent.getStringExtra("userAllData").toString())["session"].toString() /*String형*/
                showProgressDialog(getString(R.string.loading_data))
                productOverViewManager = ProductOverViewAndRentListManager()
                productOverViewManager!!.execute(session, "1", "1", "1")
            }
            1 -> {
                masterHomeLayout!!.visibility = View.GONE
                masterProductListLayout!!.visibility = View.VISIBLE
                masterProductManagementLayout!!.visibility = View.GONE
                masterInoutManagementLayout!!.visibility = View.GONE
                //masterLogLayout!!.visibility = View.GONE
                productListIdSetting()

                if (productListArrayList.isNotEmpty())
                    productListArrayList.clear()

                if (productListRecyclerView!!.adapter != null)
                    productListRecyclerView!!.adapter!!.notifyDataSetChanged()

                showProgressDialog(getString(R.string.loading_data))
                productListManager = ProductListManager()
                productListManager!!.execute(session, "1", "1", "1")
            }
            2 -> {
                masterHomeLayout!!.visibility = View.GONE
                masterProductListLayout!!.visibility = View.GONE
                masterProductManagementLayout!!.visibility = View.VISIBLE
                masterInoutManagementLayout!!.visibility = View.GONE
                //masterLogLayout!!.visibility = View.GONE

                session = JSONObject(intent.getStringExtra("userAllData").toString())["session"].toString() /*String형*/
                showProgressDialog(getString(R.string.loading_data))
                productOverViewProductManagementManager = ProductOverViewManager()
                productOverViewProductManagementManager!!.execute(session, "1", "1", "1")
                productManagementBarcodeSetting()
                productManagementSpinnerSetting()
                productManagementProductAddButtonSetting()
                productManagementProductGroupAddButtonSetting()
            }
            3 -> {
                masterHomeLayout!!.visibility = View.GONE
                masterProductListLayout!!.visibility = View.GONE
                masterProductManagementLayout!!.visibility = View.GONE
                masterInoutManagementLayout!!.visibility = View.VISIBLE

                session = JSONObject(intent.getStringExtra("userAllData").toString())["session"].toString() /*String형*/
                showProgressDialog(getString(R.string.loading_data))
                rentListManager = RentListManager()
                rentListManager!!.execute(session, "1", "1", "1")
                //masterLogLayout!!.visibility = View.GONE
            }
            /*4 -> {
                masterHomeLayout!!.visibility = View.GONE
                masterProductListLayout!!.visibility = View.GONE
                masterProductManagementLayout!!.visibility = View.GONE
                masterInoutManagementLayout!!.visibility = View.GONE
                //masterLogLayout!!.visibility = View.VISIBLE
            }*/
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
                    Toast.makeText(this@MainActivity_Master, getString(R.string.success_logout), Toast.LENGTH_SHORT).show()
                    startActivity(Intent(applicationContext, LoginActivity::class.java))
                    this@MainActivity_Master.finish()
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
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
                val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
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


    private class ProductOverViewGroupData(obj : JSONObject) {
        var groupIndex : Int? = null
        var groupName : String? = null
        var groupRentAble : Int? = null
        var groupPriority : Int? = null
        var groupCountAvailable : Int? = null
        var groupCountRent : Int? = null
        var groupCountUnavailable : Int? = null
        var groupCountBroken : Int? = null
        var groupCountRepair : Int? = null

        init {
            groupIndex = obj.get("group_index").toString().toInt()
            groupName = obj.get("group_name").toString()
            groupRentAble = obj.get("group_rentable").toString().toInt()
            groupPriority = obj.get("group_priority").toString().toInt()
            groupCountAvailable = obj.get("group_count_available").toString().toInt()
            groupCountRent = obj.get("group_count_rent").toString().toInt()
            groupCountUnavailable = obj.get("group_count_unavailable").toString().toInt()
            groupCountBroken = obj.get("group_count_broken").toString().toInt()
            groupCountRepair = obj.get("group_count_repair").toString().toInt()
        }

        override fun toString(): String {
            return groupName!!
        }
    }

    private fun settingSpinner() {
        stockSpinner = findViewById(R.id.stock_spinner)
        stockSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                val groupCountAvailable : Int = productOverViewArrayList[position].groupCountAvailable!!.toInt()
                val groupCountUnavailable : Int = productOverViewArrayList[position].groupCountUnavailable!!.toInt()
                val groupCountBroken : Int = productOverViewArrayList[position].groupCountBroken!!.toInt()
                val groupCountRepair : Int = productOverViewArrayList[position].groupCountRepair!!.toInt()
                val groupCountRent : Int = productOverViewArrayList[position].groupCountRent!!.toInt()

                stockCountTextView!!.text = ((groupCountAvailable - groupCountRent) + groupCountRent + groupCountUnavailable + groupCountBroken + groupCountRepair).toString()
                lendCountTextView!!.text = groupCountRent.toString()
                keepCountTextView!!.text = (groupCountAvailable - groupCountRent).toString()

                pieChartRenew(productOverViewArrayList[position])

            }
            override fun onNothingSelected(parent: AdapterView<*>?) { }
        }

    }

    private fun showProgressDialog(title : String) {
        progressDialog = ProgressDialog(this)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL)
        progressDialog!!.setTitle(title)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    private inner class ProductOverViewAndRentListManager : HttpPostSendProductOverViewAndRentList() {
        // string to json

        fun getProductOverViewJSONData(): JSONObject { return JSONObject(productOverViewAllData) }
        fun getProductOverViewData(): String? { return productOverViewAllData  }
        fun getRentListJSONData() : JSONObject { return JSONObject(rentListAllData) }
        fun getRentListData(): String? { return rentListAllData  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            //Log.d("ProductOverViewResult", getProductOverViewData())
            //Log.d("RentListResult", getRentListData())

            if (result == 1) {
                if (getProductOverViewJSONData()["result"] == 0) {
                    if (productOverViewArrayList.isNotEmpty())
                        productOverViewArrayList.clear()
                    for (i in 0 until JSONArray(getProductOverViewJSONData()["groups"].toString()).length())
                        productOverViewArrayList.add(ProductOverViewGroupData(JSONObject((JSONArray(getProductOverViewJSONData()["groups"].toString())[i]).toString())))

                    val spinnerAdapter = ArrayAdapter(
                        this@MainActivity_Master,
                        android.R.layout.simple_spinner_dropdown_item,
                        productOverViewArrayList
                    )
                    spinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    stockSpinner!!.adapter = spinnerAdapter
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getProductOverViewJSONData()["result"]) {
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

                if (getRentListJSONData()["result"] == 0) {
                    if (rentListArrayList.isNotEmpty())
                        rentListArrayList.clear()
                    for (i in 0 until JSONArray(getRentListJSONData()["rents"].toString()).length()) {
                        if (JSONObject((JSONArray(getRentListJSONData()["rents"].toString())[i]).toString()).get("rent_status").toString().toInt() == 1)
                            rentListArrayList.add(MasterHomeInOutData(JSONObject((JSONArray(getRentListJSONData()["rents"].toString())[i]).toString())))
                    }

                    if (rentListArrayList.isEmpty()) {
                        inOutManagementRecyclerView!!.visibility = View.GONE
                        inOutManagementRecyclerViewInsteadTextView!!.visibility = View.VISIBLE
                    }
                    else {
                        inOutManagementRecyclerView!!.visibility = View.VISIBLE
                        inOutManagementRecyclerViewInsteadTextView!!.visibility = View.GONE
                        val adapter = MasterHomeRecyclerViewAdapter(
                            session!!,
                            this@MainActivity_Master,
                            rentListArrayList
                        )
                        inOutManagementRecyclerView!!.layoutManager = LinearLayoutManager(this@MainActivity_Master)
                        inOutManagementRecyclerView!!.adapter = adapter
                    }
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getProductOverViewJSONData()["result"]) {
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
                val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
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

    abstract class HttpPostSendProductOverViewAndRentList : AsyncTask<String, Int, Int>() {

        private val productOverViewApiName = "product_overview.php"
        private val rentListApiName = "rent_list.php"

        protected var productOverViewAllData: String? = null
        protected var rentListAllData : String? = null

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

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true

            try {
                val productOverViewAPI = BackEndAPI_ProductSystemProductOverview(productOverViewApiName, defaultUseCaches, doInput, doOutput)
                productOverViewAllData = productOverViewAPI.httpsPostSend_ProductOverview(params[0])

                publishProgress(50)

                val rentListAPI =
                    BackEndAPI_RentSystemRentList(
                        rentListApiName,
                        defaultUseCaches,
                        doInput,
                        doOutput
                    )
                rentListAllData = rentListAPI.httpsPostSend_RentList(params[0])

                publishProgress(100)

            } catch (e: Exception) {
                Log.e("Master_Home Error", "Message : " + e.message)
                Log.e("Master_Home Error", "LocalizedMessage : " + e.localizedMessage)
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

    private inner class ProductOverViewManager : HttpPostSendProductOverView() {
        // string to json

        fun getProductOverViewJSONData(): JSONObject { return JSONObject(productOverViewAllData) }
        fun getProductOverViewData(): String? { return productOverViewAllData  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            //Log.d("ProductOverViewResult", getProductOverViewData())
            //Log.d("RentListResult", getRentListData())

            if (result == 1) {
                if (getProductOverViewJSONData()["result"] == 0) {
                    if (productOverViewProductManagementArrayList.isNotEmpty())
                        productOverViewProductManagementArrayList.clear()
                    for (i in 0 until JSONArray(getProductOverViewJSONData()["groups"].toString()).length())
                        productOverViewProductManagementArrayList.add(ProductOverViewGroupData(JSONObject((JSONArray(getProductOverViewJSONData()["groups"].toString())[i]).toString())))

                    val productManagementProductGroupNameSpinnerAdapter = ArrayAdapter(this@MainActivity_Master, android.R.layout.simple_spinner_dropdown_item, productOverViewProductManagementArrayList)
                    productManagementProductGroupNameSpinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    productManagementProductGroupNameSpinner!!.adapter = productManagementProductGroupNameSpinnerAdapter
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getProductOverViewJSONData()["result"]) {
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
                val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
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

    abstract class HttpPostSendProductOverView : AsyncTask<String, Int, Int>() {

        private val productOverViewApiName = "product_overview.php"

        protected var productOverViewAllData: String? = null

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

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true

            try {
                val productOverViewAPI = BackEndAPI_ProductSystemProductOverview(productOverViewApiName, defaultUseCaches, doInput, doOutput)
                publishProgress(50)
                productOverViewAllData = productOverViewAPI.httpsPostSend_ProductOverview(params[0])
                publishProgress(100)
            }
            catch (e: Exception) {
                Log.e("Product Over View Error", "Message : " + e.message)
                Log.e("Product Over View Error", "LocalizedMessage : " + e.localizedMessage)
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


    private inner class ProductListManager : HttpPostSendProductList() {
        // string to json

        fun getProductListJSONData(): JSONObject { return JSONObject(productListNameAllData) }
        fun getProductListData(): String? { return productListNameAllData  }

        fun getProductOverViewJSONData(): JSONObject { return JSONObject(productOverViewAllData) }
        fun getProductOverViewData(): String? { return productOverViewAllData  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("ProductListResult", getProductOverViewData())

            if (result == 1) {
                if (getProductListJSONData()["result"] == 0) {
                    /*
                    for (i in 0 until JSONArray(getProductListJSONData()["groups"].toString()).length())
                        productListArrayList.add(MasterProductListData(JSONObject((JSONArray(getProductListJSONData()["groups"].toString()).get(i)).toString()), JSONObject((JSONArray(getProductOverViewJSONData()["groups"].toString())[i]).toString())))

                    for (i in 0 until productListArrayList.size) {
                        for (j in 0 until JSONArray(getProductListJSONData()["products"].toString()).length()) {
                            if ((JSONObject((JSONArray(getProductListJSONData()["products"].toString()).get(j)).toString()))["product_group_index"].toString().toInt() == productListArrayList[i].groupIndex)
                                productListArrayList[i].products.put(JSONObject((JSONArray(getProductListJSONData()["products"].toString())[j]).toString()))
                        }
                    }
                     */
                    // UI 작업
                    if (productListArrayList.isEmpty()) {
                        productListRecyclerView!!.visibility = View.GONE
                        emptyProductListTextView!!.visibility = View.VISIBLE
                    }
                    else {
                        productListRecyclerView!!.visibility = View.VISIBLE
                        emptyProductListTextView!!.visibility = View.GONE

                        val adapter = MasterProductListRecyclerViewAdapter(this@MainActivity_Master, productListArrayList)
                        productListRecyclerView!!.layoutManager = LinearLayoutManager(this@MainActivity_Master)
                        productListRecyclerView!!.adapter = adapter
                    }
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getProductListJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.dont_have_permission_error)
                        -4 -> builder.setMessage(R.string.product_searching_error)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }

            }
            else {
                val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
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

    abstract inner class HttpPostSendProductList : AsyncTask<String, Int, Int>() {

        private val productListNameApiName = "product_list.php"
        private val productOverViewApiName = "product_overview.php"

        protected var productListNameAllData: String? = null
        protected var productOverViewAllData: String? = null

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

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true

            try {
                val productListAPI = BackEndAPI_ProductSystemProductList(productListNameApiName, defaultUseCaches, doInput, doOutput)

                publishProgress(10)

                productListNameAllData = productListAPI.httpsPostSend_ProductList(params[0])

                publishProgress(20)

                val productOverViewAPI = BackEndAPI_ProductSystemProductOverview(productOverViewApiName, defaultUseCaches, doInput, doOutput)

                publishProgress(30)

                productOverViewAllData = productOverViewAPI.httpsPostSend_ProductOverview(params[0])

                publishProgress(40)

                if (productListArrayList.isNotEmpty())
                    productListArrayList.clear()

                for (i in 0 until JSONArray(JSONObject(productListNameAllData)["groups"].toString()).length())
                    productListArrayList.add(MasterProductListData(JSONObject((JSONArray(JSONObject(productListNameAllData)["groups"].toString()).get(i)).toString()), JSONObject((JSONArray(JSONObject(productOverViewAllData)["groups"].toString())[i]).toString())))

                publishProgress(65)

                for (i in 0 until productListArrayList.size)
                    groupListHashMap[i] = ArrayList()

                //for (i in 0 until productListArrayList.size) {
                //    if (productListArrayList[i].products!!.toInt() > 0)
                 //       groupListHashMap[]
                //}



                for (i in 0 until productListArrayList.size) {

                    if (i == productListArrayList.size / 3)
                        publishProgress(80)

                    if (i == (productListArrayList.size / 3) * 2)
                        publishProgress(90)

                    for (j in 0 until JSONArray(JSONObject(productListNameAllData)["products"].toString()).length()) {
                        if ((JSONObject((JSONArray(JSONObject(productListNameAllData)["products"].toString()).get(j)).toString()))["product_group_index"].toString().toInt() == productListArrayList[i].groupIndex)
                            productListArrayList[i].products.put(JSONObject((JSONArray(JSONObject(productListNameAllData)["products"].toString())[j]).toString()))
                    }
                }
                publishProgress(100)
            }
            catch (e: Exception) {
                Log.e("Master_Home Error", "Message : " + e.message)
                Log.e("Master_Home Error", "LocalizedMessage : " + e.localizedMessage)
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

    private inner class ProductManagementProductAddManager : HttpPostSendProductAdd() {
        // string to json

        fun getProductAddJSONData(): JSONObject { return JSONObject(productAddAllData) }
        fun getProductAddData(): String? { return productAddAllData  }
        fun getProductNameData(): String? { return productName  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("ProductListResult", getProductAddData())

            if (result == 1) {
                if (getProductAddJSONData()["result"] == 0) {
                    Toast.makeText(this@MainActivity_Master, getProductNameData() + " " + getString(R.string.product_add_success), Toast.LENGTH_LONG).show()
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.product_add_fail)
                    when (getProductAddJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.dont_have_permission_error)
                        -4 -> builder.setMessage(R.string.product_add_fail_message)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }
            }
            else {
                val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.product_add_fail)
                builder.setMessage(R.string.product_add_fail_message)
                //if ("session") // session 로그인
                //    builder.setMessage(R.string.expire_session_error)
                builder.setPositiveButton(android.R.string.ok, null)
                builder.show()
            }

            return
        }
    }

    abstract inner class HttpPostSendProductAdd : AsyncTask<String, Int, Int>() {

        private val productAddApiName = "product_add.php"

        protected var productAddAllData: String? = null
        protected var productName : String? = null

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
            // params[4] == products : JSONArray
            // params[5] == product name

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true
            productName = params[5]

            try {
                val productAddAPI = BackEndAPI_ProductSystemProductAdd(productAddApiName, defaultUseCaches, doInput, doOutput)
                publishProgress(50)
                productAddAllData = productAddAPI.httpsPostSend_ProductAdd(params[0], JSONArray(params[4]))
                publishProgress(100)
            }
            catch (e: Exception) {
                Log.e("Product Add Error", "Message : " + e.message)
                Log.e("Product Add Error", "LocalizedMessage : " + e.localizedMessage)
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


    private inner class ProductManagementGroupAddManager : HttpPostSendGroupAdd() {
        // string to json

        fun getProductGroupAddJSONData(): JSONObject { return JSONObject(productGroupAddAllData) }
        fun getProductGroupAddData(): String? { return productGroupAddAllData  }
        fun getProductGroupNameData(): String? { return productGroupName  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {

            Log.d("ProductGroupAddResult", getProductGroupAddData())

            if (result == 1) {
                if (getProductGroupAddJSONData()["result"] == 0)
                    Toast.makeText(this@MainActivity_Master, getProductGroupNameData() + " " + getString(R.string.product_add_success), Toast.LENGTH_LONG).show()
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getProductGroupAddJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.dont_have_permission_error)
                        -4 -> builder.setMessage(R.string.product_searching_error)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }

            }
            else {
                val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.loading_data_fail)
                builder.setMessage(R.string.loading_data_fail_message)
                //if ("session") // session 로그인
                //    builder.setMessage(R.string.expire_session_error)
                builder.setPositiveButton(android.R.string.ok, null)
                builder.show()
            }
            publishProgress(100)

            if (progressDialog != null)
                progressDialog!!.dismiss()

            return
        }
    }

    abstract inner class HttpPostSendGroupAdd : AsyncTask<String, Int, Int>() {

        private val productGroupAddApiName = "product_group_add.php"

        protected var productGroupAddAllData: String? = null
        protected var productGroupName: String? = null

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
            // params[4] == product_group_name
            // params[5] == product_group_rentable
            // params[6] == product_group_priority

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true
            productGroupName = params[4]

            try {
                val productListAPI = BackEndAPI_ProductSystemProductGroupAdd(productGroupAddApiName, defaultUseCaches, doInput, doOutput)
                publishProgress(50)
                if (params[6].toInt() != -1) {
                    productGroupAddAllData = productListAPI.httpsPostSend_ProductGroupAdd(
                        params[0],
                        params[4],
                        params[5].toInt(),
                        params[6].toInt()
                    )
                    publishProgress(100)
                }
                else {
                    productGroupAddAllData = productListAPI.httpsPostSend_ProductGroupAdd(
                        params[0],
                        params[4],
                        params[5].toInt()
                    )
                    publishProgress(100)
                }
            }
            catch (e: Exception) {
                Log.e("Product Group Add Error", "Message : " + e.message)
                Log.e("Product Group Add Error", "LocalizedMessage : " + e.localizedMessage)
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


    private inner class ProductManagementSearchWithBarcode : HttpPostSendProductSearchWithBarcode() {
        // string to json

        fun getProductListBarcodeJSONData(): JSONObject { return JSONObject(productListAllData) }
        fun getProductListBarcodeData(): String? { return productListAllData  }
        fun getBarcodeData(): String? { return productBarcode  }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            //Log.d("BarcodeData", getProductListBarcodeData())

            if (result == 1) {
                if (getProductListBarcodeJSONData()["result"] == 0) {
                    if ((JSONArray((getProductListBarcodeJSONData().get("products")).toString())).length() != 0) {
                        // 제품 정보
                        val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.MyAlertDialogStyle)
                        builder.setTitle(R.string.product_info)

                        Log.d("test", (JSONArray((getProductListBarcodeJSONData().get("products")).toString())).toString())

                        val jsonObjectData = JSONObject((JSONArray((getProductListBarcodeJSONData().get("products")).toString())[0]).toString())

                        val productMessage = StringBuffer()
                        productMessage.append("이름 : ").append(jsonObjectData[productNameStr]).append("\n")
                        productMessage.append("바코드 ID : ").append(jsonObjectData[productBarcodeStr]).append("\n")
                        productMessage.append("소속 : ").append(jsonObjectData[productOwnerNameStr]).append("\n")
                        productMessage.append("구매 일자 : ").append("\n").append(jsonObjectData[productCreatedStr]).append("\n").append("\n")
                        productMessage.append("이미 존재하는 물품이라\n재고 추가를 할 수 없습니다 :(")

                        builder.setMessage(productMessage)
                        builder.setPositiveButton(android.R.string.ok, null)
                        builder.show()
                        //builder.setTitle(R.string.loading_data_fail)
                        //builder.setMessage()

                        //Log.d("test", (JSONArray((getProductListBarcodeJSONData().get("products")).toString())).toString())
                    }
                    else {
                        Toast.makeText(this@MainActivity_Master,
                            getString(R.string.the_barcode_value_is) + " " + getBarcodeData() + " 입니다!\n나머지 사항들을 기입해주세요 :)",
                            Toast.LENGTH_LONG).show()
                        productManagementProductBarcodeEdit!!.setText(getBarcodeData())
                    }
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getProductListBarcodeJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.dont_have_permission_error)
                        -4 -> builder.setMessage(R.string.product_searching_error)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }
            }
            else {
                val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
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

    abstract inner class HttpPostSendProductSearchWithBarcode : AsyncTask<String, Int, Int>() {

        private val productListApiName = "product_list.php"

        protected var productListAllData: String? = null
        protected var productBarcode : String? = null

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
            // params[4] == barcode

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true
            productBarcode = params[4]

            try {
                val productListAPI = BackEndAPI_ProductSystemProductList(productListApiName, defaultUseCaches, doInput, doOutput)
                publishProgress(50)
                productListAllData = productListAPI.httpsPostSend_ProductList(params[0], params[4], "productBarcode")
                publishProgress(100)
            }
            catch (e: Exception) {
                Log.e("Barcode Search Error", "Message : " + e.message)
                Log.e("Barcode Search Error", "LocalizedMessage : " + e.localizedMessage)
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


    private inner class RentListManager : HttpPostSendRentList() {
        // string to json

        fun getRentListNeedAllowJSONData(): JSONObject { return JSONObject(rentListNeedAllowData) }
        fun getRentListNeedAllowStrData(): String? { return rentListNeedAllowData }

        fun getRentListNeedReturnJSONData(): JSONObject { return JSONObject(rentListNeedReturnData) }
        fun getRentListNeedReturnStrData(): String? { return rentListNeedReturnData }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            //Log.d("BarcodeData", getProductListBarcodeData())

            if (result == 1) {
                if (getRentListNeedAllowJSONData()["result"] == 0) {
                    if (rentStatusArrayList.isNotEmpty())
                        rentStatusArrayList.clear()

                    var jsonArrayDataNeedAllow = getRentListNeedAllowJSONData().getJSONArray("rents")
                    for (i in 0 until jsonArrayDataNeedAllow.length())
                        rentStatusArrayList.add(MasterInOutManagementData(JSONObject(jsonArrayDataNeedAllow[i].toString())))

                    // recylerview ui 작업
                    if (rentStatusArrayList.isEmpty()) {
                        rentStatusRecyclerView!!.visibility = View.GONE
                        rentStatusRecyclerViewInsteadTextView!!.visibility = View.VISIBLE
                    }
                    else {
                        rentStatusRecyclerView!!.visibility = View.VISIBLE
                        rentStatusRecyclerViewInsteadTextView!!.visibility = View.GONE

                        val adapter = MasterInOutManagementRentStatusRecyclerViewAdapter(session!!, this@MainActivity_Master,rentStatusArrayList)
                        rentStatusRecyclerView!!.layoutManager = LinearLayoutManager(this@MainActivity_Master)
                        rentStatusRecyclerView!!.adapter = adapter
                    }
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getRentListNeedAllowJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.dont_have_permission_error)
                        -4 -> builder.setMessage(R.string.product_searching_error)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }

                if (getRentListNeedReturnJSONData()["result"] == 0) {
                    if (rentedStatusArrayList.isNotEmpty())
                        rentedStatusArrayList.clear()

                    var jsonArrayDataNeedReturn = getRentListNeedReturnJSONData().getJSONArray("rents")
                    for (i in 0 until jsonArrayDataNeedReturn.length())
                        rentedStatusArrayList.add(MasterInOutManagementData(JSONObject(jsonArrayDataNeedReturn[i].toString())))

                    if (rentedStatusArrayList.isEmpty()) {
                        rentedStatusRecyclerView!!.visibility = View.GONE
                        rentedStatusRecyclerViewInsteadTextView!!.visibility = View.VISIBLE
                    }
                    else {
                        rentedStatusRecyclerView!!.visibility = View.VISIBLE
                        rentedStatusRecyclerViewInsteadTextView!!.visibility = View.GONE
                        // recyclerview ui 작업
                        val adapter = MasterInOutManagementRentedStatusRecyclerViewAdapter(session!!, this@MainActivity_Master,rentedStatusArrayList)
                        rentedStatusRecyclerView!!.layoutManager = LinearLayoutManager(this@MainActivity_Master)
                        rentedStatusRecyclerView!!.adapter = adapter
                    }
                }
                else {
                    val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                    builder.setTitle(R.string.loading_data_fail)
                    when (getRentListNeedAllowJSONData()["result"]) {
                        -1 -> builder.setMessage(R.string.implementation_error)
                        -2 -> builder.setMessage(R.string.server_error)
                        -3 -> builder.setMessage(R.string.dont_have_permission_error)
                        -4 -> builder.setMessage(R.string.product_searching_error)
                    }
                    //if ("session") // session 로그인
                    //    builder.setMessage(R.string.expire_session_error)
                    builder.setPositiveButton(android.R.string.ok, null)
                    builder.show()
                }
            }
            else {
                val builder = AlertDialog.Builder(this@MainActivity_Master, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.loading_data_fail)
                builder.setMessage(R.string.loading_data_fail_message)
                //if ("session") // session 로그인
                //    builder.setMessage(R.string.expire_session_error)
                builder.setPositiveButton(android.R.string.ok, null)
                builder.show()
            }

            if (progressDialog != null)
                progressDialog!!.dismiss()

            return
        }
    }

    abstract inner class HttpPostSendRentList : AsyncTask<String, Int, Int>() {

        private val rentListApiName = "rent_list.php"

        protected var rentListNeedAllowData : String? = null
        protected var rentListNeedReturnData : String? = null

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

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true

            try {

                publishProgress(40)

                val rentListAPI1 = BackEndAPI_RentSystemRentList(rentListApiName, defaultUseCaches, doInput, doOutput)
                rentListNeedAllowData = rentListAPI1.httpsPostSend_RentList(params[0], "1", "rentStatus")
                publishProgress(75)

                val rentListAPI2 = BackEndAPI_RentSystemRentList(rentListApiName, defaultUseCaches, doInput, doOutput)
                rentListNeedReturnData = rentListAPI2.httpsPostSend_RentList(params[0], "2", "rentStatus")
                publishProgress(100)

            } catch (e: Exception) {
                Log.e("Master_Home Error", "Message : " + e.message)
                Log.e("Master_Home Error", "LocalizedMessage : " + e.localizedMessage)
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


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        // QR코드/ 바코드를 스캔한 결과
        var result : IntentResult = IntentIntegrator.parseActivityResult(requestCode, resultCode, data)
        // result.getFormatName() : 바코드 종류
        // result.getContents() : 바코드 값
        //Toast.makeText(this, getString(R.string.the_barcode_value_is) + " " + result.contents.toString() + " 입니다 :)", Toast.LENGTH_LONG).show()

        // 이미 있는 바코드인지 검색한다.
        // 이미 있다면, Rent 가능한지?
        // 없다면 추가를 도와준다
        //showProgressDialog(getString(R.string.searching_barcode))
        //var barcodeSearch = ProductManagementSearchWithBarcode()
        //barcodeSearch.execute(session, "1", "1", "1", result.contents.toString())

        when {
            masterHomeLayout!!.visibility == View.VISIBLE -> {

            }
            masterProductListLayout!!.visibility == View.VISIBLE -> {

            }
            masterProductManagementLayout!!.visibility == View.VISIBLE -> {
                if (result.contents != null) {
                    showProgressDialog(getString(R.string.searching_barcode))
                    barcodeSearch = ProductManagementSearchWithBarcode()
                    barcodeSearch!!.execute(session, "1", "1", "1", result.contents.toString())
                }
            }
            masterInoutManagementLayout!!.visibility == View.VISIBLE -> {

            }
            //masterLogLayout!!.visibility == View.VISIBLE -> {
//
           // }
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

    override fun onDestroy() {
        super.onDestroy()
        rentListArrayList.clear()
        productOverViewProductManagementArrayList.clear()
        inOutManagementRecyclerView = null
        inOutManagementRecyclerViewInsteadTextView = null
    }
}