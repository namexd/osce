package com.mx.osce.broadcast;

import com.google.gson.Gson;
import com.mx.osce.BaseActivity;
import com.mx.osce.LoginActivity;
import com.mx.osce.MainActivity;
import com.mx.osce.MyApplicaton;
import com.mx.osce.R;
import com.mx.osce.bean.EndExamBean;
import com.mx.osce.bean.LoginBean;
import com.mx.osce.bean.StartExamBean;
import com.mx.osce.exception.ControlException;
import com.mx.osce.fragment.FragmentDrawWait;
import com.mx.osce.fragment.FragmentGrade;
import com.mx.osce.util.Constant;
import com.mx.osce.util.Utils;

import android.app.Activity;
import android.app.Fragment;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.WindowManager;
import android.widget.Toast;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;

public class ForceBroadReciver extends BroadcastReceiver {

	public static final String TAG = "ForceEndBroadReciver";
	private SweetAlertDialog mTipsDialog;
	private String mExam_screening_id = null;
	private String mStudent_id = null;
	private String mIsWarn = null;
	private String mExamType = null;

	@Override
	public void onReceive(Context context, Intent intent) {
		Activity currentActivity = ((MyApplicaton) context.getApplicationContext()).currentActivity();
		String currentActName = currentActivity.getLocalClassName();
		String message = intent.getStringExtra("Message");
		mExam_screening_id = Utils.getSharedPrefrences(context, "exam_screening_id");
		mStudent_id = Utils.getSharedPrefrences(context, "student_id");
		mIsWarn = Utils.getSharedPrefrences(context, "warn");
		mExamType = Utils.getSharedPrefrences(context, "type");

		switch (intent.getAction()) {

		case Constant.ACTION_GIVE_UP_EXAM:// 放弃考试

			ControlException.recoverException2Nomal(context);// 解除异常绑定

			switch (currentActName) {

			case "MainActivity":
				EndExamBean endGiveUpInMain = null;
				try {
					endGiveUpInMain = new Gson().fromJson(message, EndExamBean.class);
					if (mExam_screening_id.equals(endGiveUpInMain.getData().getExam_screening_id())
							&& endGiveUpInMain.getData().getStudent_id().equals(mStudent_id)) {

						judgeMainActivity(context, currentActivity, "该考生确认为放弃考试");
					}
				} catch (Exception e) {
					Toast.makeText(context, "确认考生放弃考试，推送数据出错！", Toast.LENGTH_LONG).show();
					break;
				}
				break;
			case "GradeActivity":
				EndExamBean endGiveUpInGrade = null;
				try {
					endGiveUpInGrade = new Gson().fromJson(message, EndExamBean.class);
					if (mExam_screening_id.equals(endGiveUpInGrade.getData().getExam_screening_id())
							&& endGiveUpInGrade.getData().getStudent_id().equals(mStudent_id)) {
						judgeGradeActivity(context, currentActivity, "该考生确认为放弃考试");
					}
				} catch (Exception e) {
					Toast.makeText(context, "确认考生放弃考试，推送数据出错！", Toast.LENGTH_LONG).show();
					break;
				}
				break;
			case "VideoActivity":
				EndExamBean endGiveUpInVideo = null;
				try {
					endGiveUpInVideo = new Gson().fromJson(message, EndExamBean.class);

					if (mExam_screening_id.equals(endGiveUpInVideo.getData().getExam_screening_id())
							&& endGiveUpInVideo.getData().getStudent_id().equals(mStudent_id)) {
						judgeVideoActivity(context, currentActivity, "该考生确认为放弃考试");
					}
				} catch (Exception e) {
					Toast.makeText(context, "确认考生放弃考试，推送数据出错！", Toast.LENGTH_LONG).show();
					break;
				}
				break;
			default:
				break;
			}
			break;

		case Constant.ACTION_WARN_EXAM_END:// 确认作弊

			ControlException.recoverException2Nomal(context);// 解除异常绑定
			switch (currentActName) {
			case "MainActivity":
				EndExamBean endWarnInMain = null;
				try {
					endWarnInMain = new Gson().fromJson(message, EndExamBean.class);
					if (mExam_screening_id.equals(endWarnInMain.getData().getExam_screening_id())
							&& endWarnInMain.getData().getStudent_id().equals(mStudent_id)) {
						judgeMainActivity(context, currentActivity, "该考生确认为作弊");
					}
				} catch (Exception e) {
					Toast.makeText(context, "确认考生放弃作弊，推送数据出错！", Toast.LENGTH_LONG).show();
					break;
				}
				break;
			case "GradeActivity":
				EndExamBean endWarnInGrade = null;
				try {
					endWarnInGrade = new Gson().fromJson(message, EndExamBean.class);
					if (mExam_screening_id.equals(endWarnInGrade.getData().getExam_screening_id())
							&& endWarnInGrade.getData().getStudent_id().equals(mStudent_id)) {
						judgeGradeActivity(context, currentActivity, "该考生确认为作弊");
					}
				} catch (Exception e) {
					Toast.makeText(context, "确认考生放弃作弊，推送数据出错！", Toast.LENGTH_LONG).show();
					break;
				}
				break;
			case "VideoActivity":
				EndExamBean endWarnInVideo = null;
				try {
					endWarnInVideo = new Gson().fromJson(message, EndExamBean.class);
					if (mExam_screening_id.equals(endWarnInVideo.getData().getExam_screening_id())
							&& endWarnInVideo.getData().getStudent_id().equals(mStudent_id)) {
						judgeVideoActivity(context, currentActivity, "该考生确认为作弊");
					}
				} catch (Exception e) {
					Toast.makeText(context, "确认考生放弃作弊，推送数据出错！", Toast.LENGTH_LONG).show();
					break;
				}
				break;
			default:
				break;
			}

			break;
		case Constant.ACTION_FORCE_OFFINE:// 强制下线
			LoginBean loginBean = new Gson().fromJson(message, LoginBean.class);
			if (loginBean.getResultBean().getUser_id().equals(Utils.getSharedPrefrences(context, "user_id"))
					&& !loginBean.getResultBean().getAccess_token()
							.equals(Utils.getSharedPrefrences(context, "token"))) {
				// forceOffLine(context);
				Log.e("DSX", "强制下线");
				mTipsDialog = new SweetAlertDialog(context, SweetAlertDialog.WARNING_TYPE,false);
				mTipsDialog.setTitleText("检测到你的账号在其他终端登录!");
				mTipsDialog.setContentText("你已被强制下线");
				mTipsDialog.getWindow().setType(WindowManager.LayoutParams.TYPE_SYSTEM_ALERT);
				mTipsDialog.show();
				Intent broadIntent = new Intent(context, LoginActivity.class);
				broadIntent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
				context.startActivity(broadIntent);
			}
			break;
		}
	}

	// 如果是MainActivity,提示对话框，刷新页面
	private void judgeMainActivity(Context context, Activity currentActivity, String contentText) {
		mTipsDialog = new SweetAlertDialog(context, SweetAlertDialog.ERROR_TYPE,false);
		mTipsDialog.getWindow().setType(WindowManager.LayoutParams.TYPE_SYSTEM_ALERT);
		mTipsDialog.setTitleText("考试已被终止");
		if (contentText != null) {
			mTipsDialog.setContentText(contentText);
		}
		mTipsDialog.show();
		currentActivity.getFragmentManager().beginTransaction().replace(R.id.fragment_draw, new FragmentDrawWait())
				.commit();
	}

	// 评分页面，结束考生状态，跳转到评价碎片
	private void judgeGradeActivity(Context context, Activity currentActivity, String contentText) {
		boolean isGrade = currentActivity.getFragmentManager().findFragmentById(R.id.frame) instanceof FragmentGrade;
		if (isGrade) {// 评分碎片
			mTipsDialog = new SweetAlertDialog(context, SweetAlertDialog.ERROR_TYPE,false);
			mTipsDialog.getWindow().setType(WindowManager.LayoutParams.TYPE_SYSTEM_ALERT);
			mTipsDialog.setTitleText("考试已被终止");
			if (contentText != null) {
				mTipsDialog.setContentText(contentText);
			}
			mTipsDialog.show();
			FragmentGrade grade = (FragmentGrade) currentActivity.getFragmentManager().findFragmentById(R.id.frame);
			grade.changeTestStatu();
		} else {// 评价碎片
			mTipsDialog = new SweetAlertDialog(context, SweetAlertDialog.ERROR_TYPE,false);
			mTipsDialog.getWindow().setType(WindowManager.LayoutParams.TYPE_SYSTEM_ALERT);
			mTipsDialog.setTitleText("考试已被终止");
			if (contentText != null) {

				mTipsDialog.setContentText(contentText);
			}
			mTipsDialog.show();
		}
	}

	// 结束视频的活动，再次判断
	private void judgeVideoActivity(Context context, Activity currentActivity, String contentText) {
		currentActivity.finish();// 结束视频的活动
		Activity newActivity = ((MyApplicaton) context.getApplicationContext()).currentActivity();
		String newActivityName = newActivity.getLocalClassName();
		switch (newActivityName) {
		case "MainActivity":
			judgeMainActivity(context, newActivity, contentText);
			break;
		case "GradeActivity":
			judgeGradeActivity(context, newActivity, contentText);
			break;
		default:
			break;
		}
	}
}
