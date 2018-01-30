package com.mx.test.exception;

import com.mx.test.util.Utils;

import android.content.Context;

public class ControlException {

	/**
	 * 检查打分是否是正常退出
	 * 
	 * @return 异常返回false
	 */
	public static boolean checkGradeIsNormal(Context context) {
		if ("true".equals(Utils.getSharedPrefrencesByName(context, "Exception", "gradeIsNormalEnd"))
				|| null == (Utils.getSharedPrefrencesByName(context, "Exception", "gradeIsNormalEnd"))) {
			return true;
		}
		return false;
	}

	/**
	 * 检查评价是否是正常退出
	 * 
	 * @return 异常返回false
	 */
	public static boolean checkEvaluateIsNormal(Context context) {

		if ("true".equals(Utils.getSharedPrefrencesByName(context, "Exception", "evaluateIsNormalEnd"))
				|| null == (Utils.getSharedPrefrencesByName(context, "Exception", "evaluateIsNormalEnd"))) {
			return true;
		}

		return false;
	}

	public static void recoverException2Nomal(Context context) {
		Utils.saveSharedPrefrencesByName(context, "Exception", "gradeIsNormalEnd", null);
		Utils.saveSharedPrefrencesByName(context, "Exception", "evaluateIsNormalEnd", null);
	}

	public static boolean checkStudentStateIsEnd(Context context) {
		if (Utils.getSharedPrefrences(context, "endTime") == null) {
			return false;
		} else {
			return true;
		}
	}
}
