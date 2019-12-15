package kr.ac.sejong.gotgan.Main.MasterFragment

import android.app.ProgressDialog
import android.graphics.Color
import android.os.AsyncTask
import androidx.fragment.app.Fragment
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.AdapterView
import android.widget.ArrayAdapter
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.widget.AppCompatSpinner
import androidx.appcompat.widget.AppCompatTextView
import androidx.recyclerview.widget.RecyclerView
import com.github.mikephil.charting.animation.Easing
import com.github.mikephil.charting.charts.PieChart
import com.github.mikephil.charting.data.PieData
import com.github.mikephil.charting.data.PieDataSet
import com.github.mikephil.charting.data.PieEntry
import kotlinx.android.synthetic.main.fragment_main_master_home.*
import kr.ac.sejong.gotgan.BackEndAPI.ProductSystem.BackEndAPI_ProductSystemProductOverview
import kr.ac.sejong.gotgan.R
import org.json.JSONArray
import org.json.JSONObject

class MainFragment_MasterHome : Fragment() {

    private var progressDialog : ProgressDialog? = null
    private var session : String? = null
    private var productOverViewManager : ProductOverViewManager? = null
    private var stockSpinner : AppCompatSpinner? = null
    private var stockPieChart : PieChart? = null
    private var productOverViewArrayList : ArrayList<ProductOverViewGroupData> = ArrayList()

    private var stockCountTextView: AppCompatTextView? = null
    private var lendCountTextView: AppCompatTextView? = null
    private var keepCountTextView: AppCompatTextView? = null

    private var inOutManageMentRecyclerView : RecyclerView? = null

    companion object {

        // 자바에서 정적 메서드(static method)처럼 사용할 수 있도록 함
        @JvmStatic fun getInstance(_session : String) : Fragment {
            val masterHomeFragment = MainFragment_MasterHome()

            val args = Bundle()
            args.putString("session", _session)
            masterHomeFragment.arguments = args

            return masterHomeFragment
        }
    }

    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?): View? {

        val rootView: ViewGroup = inflater.inflate(R.layout.fragment_main_master_home, container, false) as ViewGroup

        idSetting(rootView)
        pieChartBaseSetting()
        settingSpinner(rootView)

        if (arguments != null) {
            session = arguments!!.getString("session")
            Log.i("session", session)
            showLoginDialog(getString(R.string.loading_data))
            productOverViewManager = ProductOverViewManager()
            productOverViewManager!!.execute(session, "1", "1", "1")
        }

        return rootView
    }

    private fun pieChartBaseSetting() {
        stockPieChart!!.setUsePercentValues(false) // 퍼센트 값으로 설정
        stockPieChart!!.isDragDecelerationEnabled = false // 돌리기 방지
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
            pieValues.add(PieEntry(productOverView.groupCountAvailable!!.toFloat(), getString(R.string.available_count)))
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
        stockPieChart!!.centerText = productOverView.groupName

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

    private fun idSetting(rootView : ViewGroup) {
        stockCountTextView = rootView.findViewById(R.id.stock_count_textView)
        lendCountTextView = rootView.findViewById(R.id.lend_count_textView)
        keepCountTextView = rootView.findViewById(R.id.keep_count_textView)
        stockPieChart = rootView.findViewById(R.id.stock_pieChart)
        inOutManageMentRecyclerView = rootView.findViewById(R.id.import_export_management_recyclerView)
    }

    private fun settingSpinner(rootView : ViewGroup) {
        stockSpinner = rootView.findViewById(R.id.stock_spinner)
        stockSpinner!!.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                val groupCountAvailable : Int = productOverViewArrayList[position].groupCountAvailable!!.toInt()
                val groupCountUnavailable : Int = productOverViewArrayList[position].groupCountUnavailable!!.toInt()
                val groupCountBroken : Int = productOverViewArrayList[position].groupCountBroken!!.toInt()
                val groupCountRepair : Int = productOverViewArrayList[position].groupCountRepair!!.toInt()
                val groupCountRent : Int = productOverViewArrayList[position].groupCountRent!!.toInt()

                stockCountTextView!!.text = (groupCountAvailable + groupCountUnavailable + groupCountBroken + groupCountRepair + groupCountRent).toString()
                lendCountTextView!!.text = groupCountRent.toString()
                keepCountTextView!!.text = groupCountAvailable.toString()

                pieChartRenew(productOverViewArrayList[position])

            }
            override fun onNothingSelected(parent: AdapterView<*>?) { }
        }

    }

    private fun showLoginDialog(title : String) {
        progressDialog = ProgressDialog(activity)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL)
        progressDialog!!.setTitle(title)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    private inner class ProductOverViewManager : HttpPostSendProductOverview() {
        // string to json

        fun getJSONData(): JSONObject {
            return JSONObject(allData)
        }

        fun getData(): String? {
            return allData
        }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("result", getData())

            if (result == 1 && getJSONData()["result"] == 0) {
                for (i in 0 until JSONArray(getJSONData()["groups"].toString()).length())
                    productOverViewArrayList.add(ProductOverViewGroupData(JSONObject((JSONArray(getJSONData()["groups"].toString())[i]).toString())))

                val spinnerAdapter = ArrayAdapter(activity!!, android.R.layout.simple_spinner_dropdown_item, productOverViewArrayList)
                spinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                stock_spinner.adapter = spinnerAdapter
            }
            else {
                val builder = AlertDialog.Builder(activity!!, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.loading_data_fail)
                when (getJSONData()["result"]) {
                    -1 -> builder.setMessage(R.string.implementation_error)
                    -2 -> builder.setMessage(R.string.server_error)
                    -3 -> builder.setMessage(R.string.dont_have_permission_error)
                    -4 -> builder.setMessage(R.string.searching_error)
                }
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

    abstract class HttpPostSendProductOverview : AsyncTask<String, Int, Int>() {

        private val apiName = "product_overview.php"
        var allData: String? = null

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

            publishProgress(30)
            try {
                val productOverViewAPI =
                    BackEndAPI_ProductSystemProductOverview(
                        apiName,
                        defaultUseCaches,
                        doInput,
                        doOutput
                    )
                publishProgress(50)
                allData = productOverViewAPI.httpsPostSend_ProductOverview(params[0])
                publishProgress(100)
            } catch (e: Exception) {
                Log.e("ProductOverView Error", "Message : " + e.message)
                Log.e("ProductOverView Error", "LocalizedMessage : " + e.localizedMessage)
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

}