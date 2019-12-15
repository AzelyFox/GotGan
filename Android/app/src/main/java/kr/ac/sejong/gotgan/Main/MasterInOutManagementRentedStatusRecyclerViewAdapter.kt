package kr.ac.sejong.gotgan.Main

import android.app.ProgressDialog
import android.content.Context
import android.os.AsyncTask
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.widget.AppCompatTextView
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton
import kr.ac.sejong.gotgan.BackEndAPI.RentSystem.BackEndAPI_RentSystemRentAllow
import kr.ac.sejong.gotgan.BackEndAPI.RentSystem.BackEndAPI_RentSystemRentReturn
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.inOutManagementRecyclerView
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.inOutManagementRecyclerViewInsteadTextView
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.rentListArrayList
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.rentedStatusArrayList
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.rentedStatusRecyclerView
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.rentedStatusRecyclerViewInsteadTextView
import kr.ac.sejong.gotgan.R
import org.json.JSONObject

class MasterInOutManagementRentedStatusRecyclerViewAdapter(_session : String, _context : Context, _InoutManagementArrayList : ArrayList<MasterInOutManagementData>) : RecyclerView.Adapter<MasterInOutManagementRentedStatusRecyclerViewAdapter.ViewHolder>() {

    private var InoutManagementArrayList : ArrayList<MasterInOutManagementData>? = null
    private var progressDialog : ProgressDialog? = null
    private var context : Context? = null
    private var session : String? = null

    init {
        session = _session
        context = _context
        InoutManagementArrayList = _InoutManagementArrayList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        return ViewHolder(LayoutInflater.from(parent.context).inflate(R.layout.inout_management_rented_status_item, parent, false))
    }

    override fun getItemCount(): Int {
        return InoutManagementArrayList!!.size
    }

    private fun showProgressDialog(title : String, _context : Context) {
        progressDialog = ProgressDialog(_context)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL)
        progressDialog!!.setTitle(title)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.inoutManagementRentedStatusNameTextView.text = InoutManagementArrayList!![position].rentUserName
        holder.inoutManagementRentedStatusProductNameTextView.text = InoutManagementArrayList!![position].rentProductName
        holder.inoutManagementRentedStatusStartDateTextView.text = InoutManagementArrayList!![position].rentTimeStart
        holder.inoutManagementRentedStatusEndDateTextView.text = InoutManagementArrayList!![position].rentTimeEnd
        holder.inoutManagementRentedStatusAllowButton.setOnClickListener {
            // rent_return
            showProgressDialog(context!!.resources.getString(R.string.processing), context!!)
            val returnTask = RecyclerViewRentReturnManager(context!!)
            // params[0] == session
            // params[1] == defaultUseCaches
            // params[2] == doInput
            // params[3] == doOutput
            // params[4] == rentIndex or productBarcode
            // params[5] == mode
            // params[6] == rentProductName
            // params[7] == arrayListIndex
            returnTask.execute(session, "0", "1", "1", InoutManagementArrayList!![position].rentIndex.toString(), "rentIndex", InoutManagementArrayList!![position].rentProductName, position.toString())
        }
    }

    inner class ViewHolder (view : View) : RecyclerView.ViewHolder(view) {
        var inoutManagementRentedStatusNameTextView : AppCompatTextView = view.findViewById(R.id.inout_management_rented_status_name_textView)
        var inoutManagementRentedStatusProductNameTextView : AppCompatTextView = view.findViewById(R.id.inout_management_rented_status_product_name_textView)
        var inoutManagementRentedStatusStartDateTextView : AppCompatTextView = view.findViewById(R.id.inout_management_rented_status_start_date_textView)
        var inoutManagementRentedStatusEndDateTextView : AppCompatTextView = view.findViewById(R.id.inout_management_rented_status_end_date_textView)
        var inoutManagementRentedStatusAllowButton : MaterialButton = view.findViewById(R.id.inout_management_rented_status_allow_button)
    }

    private inner class RecyclerViewRentReturnManager(_context : Context) : HttpPostSendRendReturn() {
        // string to json

        val context = _context

        fun getRentReturnResultJSONData(): JSONObject { return JSONObject(allResultData) }
        fun getRentReturnResultData(): String? { return allResultData  }
        fun getProductName() : String? { return rentProductName }
        fun getRentModeName() : String? { return modeName }
        fun getALIndex() : Int? { return arrayListIndex }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("Rent AorD Result", getRentReturnResultData())

            if (result == 1 && getRentReturnResultJSONData()["result"] == 0) {
                // 성공시 처리
                Toast.makeText(context, getProductName() + " - " + context.resources.getString(R.string.success_return_message), Toast.LENGTH_LONG).show()

                rentedStatusArrayList.removeAt(getALIndex()!!)

                if (rentedStatusArrayList.isEmpty()) {
                    rentedStatusRecyclerView!!.visibility = View.GONE
                    rentedStatusRecyclerViewInsteadTextView!!.visibility = View.VISIBLE
                }
                else {
                    rentedStatusRecyclerView!!.visibility = View.VISIBLE
                    rentedStatusRecyclerViewInsteadTextView!!.visibility = View.GONE
                }
                rentedStatusRecyclerView!!.adapter = rentedStatusRecyclerView!!.adapter
            }
            else {
                val builder = AlertDialog.Builder(context, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.loading_data_fail)
                when (getRentReturnResultJSONData()["result"]) {
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
            return
        }
    }

    abstract class HttpPostSendRendReturn: AsyncTask<String, Int, Int>() {

        private val rentAllowApiName = "rent_return.php"

        protected var allResultData: String? = null

        protected var modeName : String? = null
        protected var rentProductName : String? = null

        protected var arrayListIndex : Int? = null

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
            // params[4] == rentIndex or productBarcode
            // params[5] == mode
            // params[6] == rentProductName
            // params[7] == arrayListIndex

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true
            modeName = params[5]
            rentProductName = params[6]
            arrayListIndex = params[7].toInt()

            try {
                val rentAllowAPI = BackEndAPI_RentSystemRentReturn(rentAllowApiName, defaultUseCaches, doInput, doOutput)
                publishProgress(50)
                allResultData = rentAllowAPI.httpsPostSend_RentReturn(params[0], params[4], params[5])
                publishProgress(100)
            }
            catch (e: Exception) {
                Log.e("Rented Error", "Message : " + e.message)
                Log.e("Rented Error", "LocalizedMessage : " + e.localizedMessage)
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
