/*package com.mx.bluetooth.adapter;

import java.util.ArrayList;

import com.mx.bluetooth.R;
import com.mx.bluetooth.R.raw;
import com.mx.bluetooth.bean.GradePointBean_Local;
import com.mx.bluetooth.custom.CustomSeekBar;
import com.mx.bluetooth.log.SaveNetRequestLog2Local;
import com.mx.bluetooth.util.Constant;
import com.mx.bluetooth.util.Utils;
import android.annotation.SuppressLint;
import android.content.Context;
import android.content.res.ColorStateList;
import android.graphics.Color;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.EditText;
import android.widget.TextView;

@SuppressLint("ResourceAsColor")
public class ListViewAdapter extends BaseAdapter {

	public static final String TAG = "ListViewAdapter";

	*//** 使用上下文 *//*
	private Context context;

	*//** listview只是展示单个的考核项 *//*
	private ArrayList<GradePointBean_Local> allPoints;

	private int pointIndex = 0;

	private boolean showPoint = false;

	private GetScoreInterface mGetScoreInterface;

	*//**
	 * 是否显示考点
	 * 
	 * @param isShow
	 *            是否要显示评分标准
	 * @return 返回true,显示；返回false,隐藏
	 *//*
	public void isShowItemPoint() {
		showPoint = true;
		this.notifyDataSetChanged();
	}

	public void isHideItemPoint() {
		showPoint = false;
		this.notifyDataSetChanged();
	}

	public void changTextColar() {
		this.notifyDataSetChanged();
	}

	public ListViewAdapter(Context context, ArrayList<GradePointBean_Local> allPoints,
			GetScoreInterface mGetScoreInterface) {
		this.context = context;
		this.mGetScoreInterface = mGetScoreInterface;
		this.allPoints = new ArrayList<GradePointBean_Local>();
		if (allPoints != null) {
			this.allPoints.addAll(allPoints);
		}

		if (Utils.getSharedPrefrences(context, "teacher_type").equals(Constant.TEACHER_TYPE_SP)) {
			for (int i = 0; i < this.allPoints.size(); i++) {
				if (this.allPoints.get(i).getTag().equalsIgnoreCase(Constant.NORMAL_TAG)) {
					for (int j = 0; j < this.allPoints.get(i).getTest_term().size(); j++) {
						if (this.allPoints.get(i).getTest_term().get(j).getScore().equalsIgnoreCase("1")) {
							this.allPoints.get(i).getTest_term().get(j).setReal("1");
							this.allPoints.get(i).getTest_term().get(j).setScored(true);
						}
					}
				}
			}
		}
	}

	public void setGradePointBeanIndex(int index) {
		this.pointIndex = index;
	}

	public ArrayList<GradePointBean_Local> getList() {
		return allPoints;
	}

	@Override
	public int getViewTypeCount() {

		return super.getViewTypeCount() + 2;
	}

	public int getCount() {
		// String ddd="";
		String tag = allPoints.get(pointIndex).getTag();
		// return allPoints.get(pointIndex).getTest_term().size();

		return tag.equals(Constant.SPECIAL_TAG) ? 1 : allPoints.get(pointIndex).getTest_term().size();
	}

	public Object getItem(int position) {
		String tag = allPoints.get(pointIndex).getTag();
		// return allPoints.get(pointIndex).getTest_term().get(position);
		return tag.equals(Constant.SPECIAL_TAG) ? allPoints.get(position)
				: allPoints.get(pointIndex).getTest_term().get(position);
	}

	public long getItemId(int position) {
		return 0;
	}

	public View getView(final int position, View convertView, ViewGroup parent) {

		ViewHolderSpec holderSpec = new ViewHolderSpec();

		ViewHolderNor holder = new ViewHolderNor(position);

		String dataType = allPoints.get(pointIndex).getTag();

		if (dataType.equals(Constant.NORMAL_TAG)) {

			convertView = LayoutInflater.from(context).inflate(R.layout.listview_item, null);

			holder.item_number = (TextView) convertView.findViewById(R.id.tv_fragment_test_detail_point_item_num);
			holder.item_content = (TextView) convertView.findViewById(R.id.tv_fragment_test_detail_point_item_content);
			// if
			// (allPoints.get(pointIndex).getTest_term().get(position).getColor()!=0)
			// {
			// holder.item_number.setTextColor(Color.parseColor("#ed5565"));
			// holder.item_content.setTextColor(Color.parseColor("#ed5565"));
			// }
			holder.item_ponit = (TextView) convertView.findViewById(R.id.tv_fragment_test_detaile_point_item_point);
			holder.pointBar = (CustomSeekBar) convertView.findViewById(R.id.seekBar_tg2);
			// 初始化SeekBar
			// int size = allPoints.get(pointIndex).getTest_term().size();

			String[] Arr = Utils
					.getStringArray(Integer.valueOf(allPoints.get(pointIndex).getTest_term().get(position).getScore()));
			int length = Arr.length;
			String[] Are_Ex = new String[length + 1];
			Are_Ex[0] = "0";
			System.arraycopy(Arr, 0, Are_Ex, 1, length);

			holder.pointBar.setViewPara(Are_Ex.length, Are_Ex, 0, 1000);
			holder.pointBar.reDraw();
			holder.pointBar.setOnSeekBarChangeListener(holder);

			// 普通技能考站
			// if (Utils.getSharedPrefrences(context,
			// "teacher_type").equals(Constant.TEACHER_TYPE_SP)) {
			//
			// for (int i = 0; i <=
			// allPoints.get(pointIndex).getTest_term().size() - 1; i++) {
			//
			// int score =
			// Integer.parseInt(allPoints.get(pointIndex).getTest_term().get(i).getScore());
			//
			// if (score == 1) {
			//
			// if
			// (allPoints.get(pointIndex).getTest_term().get(i).getReal().equalsIgnoreCase("0"))
			// {
			// allPoints.get(pointIndex).getTest_term().get(i).setScored(true);
			// allPoints.get(pointIndex).getTest_term().get(i).setReal("0");
			// } else {
			// allPoints.get(pointIndex).getTest_term().get(i).setReal("1");
			// allPoints.get(pointIndex).getTest_term().get(i).setScored(true);
			// }
			// }
			// }
			// }

			if (allPoints.get(pointIndex).getTest_term().get(position).isScored()) {

				int index = Integer.parseInt(allPoints.get(pointIndex).getTest_term().get(position).getReal());
				holder.pointBar.selectMarkItem(index);
				holder.pointBar.reSetMarkTextColors();
				holder.pointBar.setMarkTextColor(index, context.getResources().getColor(R.color.red_btn_bg_color));
			} else {

				// SP考站，默认分数为满分
				if (Utils.getSharedPrefrences(context, "teacher_type").equals(Constant.TEACHER_TYPE_SP)) {

					int score = Integer.parseInt(allPoints.get(pointIndex).getTest_term().get(position).getScore());

					if (score == 1) {
						holder.pointBar.selectMarkItem(score);
						holder.pointBar.setMarkTextColor(score,
								context.getResources().getColor(R.color.red_btn_bg_color));
						// holder.pointBar.reSetMarkTextColors();
						allPoints.get(pointIndex).getTest_term().get(position).setReal(score + "");
						allPoints.get(pointIndex).getTest_term().get(position).setScored(true);
					} else {
						holder.pointBar.hidePopup();
						holder.pointBar.reSetMarkTextColors();
					}
				}
			}

			// convertView.setTag(holder);
		} else if (dataType.equals(Constant.SPECIAL_TAG)) {

			convertView = LayoutInflater.from(context).inflate(R.layout.listview_item_special, null);

			holderSpec.item_number_spec = (TextView) convertView
					.findViewById(R.id.tv_fragment_test_detail_point_item_num_spec);
			holderSpec.item_content_spec = (TextView) convertView
					.findViewById(R.id.tv_fragment_test_detail_point_item_content_spec);
			holderSpec.item_subtractPoint_spec = (EditText) convertView.findViewById(R.id.editPoint_spec);
			// convertView.setTag(holderSpec);
		}

		if (dataType.equals(Constant.NORMAL_TAG)) {
			if (allPoints.get(pointIndex).getTest_term().get(position).isScored()) {

			} else {
				holder.pointBar.hidePopup();
			}
			holder.item_number.setText("" + allPoints.get(pointIndex).getTest_term().get(position).getSort());
			holder.item_content.setText(allPoints.get(pointIndex).getTest_term().get(position).getContent());

			if (showPoint) {// 显示/隐藏 考点
				holder.item_ponit.setText(allPoints.get(pointIndex).getTest_term().get(position).getAnswer());
				holder.item_ponit.setVisibility(View.VISIBLE);
			} else {
				holder.item_ponit.setVisibility(View.GONE);
			}
		} else if (dataType.equals(Constant.SPECIAL_TAG)) {

			holderSpec.item_number_spec.setText("特殊考点");

			holderSpec.item_content_spec.setText(allPoints.get(pointIndex).getTitle());

			holderSpec.item_subtractPoint_spec.setHint("总分:" + allPoints.get(pointIndex).getScore());

			holderSpec.item_subtractPoint_spec.addTextChangedListener(new TextWatcher() {

				@Override
				public void onTextChanged(CharSequence s, int start, int before, int count) {
					int totalScore = Integer.parseInt(allPoints.get(pointIndex).getScore());// 特殊考点总数
					long realScore;

					if (s.length() > 0) {
						if (s.length() < 4) {
							long subtractScore = Long.parseLong(s.toString().trim());// 扣分总数
							realScore = (subtractScore >= totalScore) ? 0 : (totalScore - subtractScore);// 实际得分
						} else {
							realScore = 0;
						}
					} else {
						realScore = totalScore;
					}

					if (!allPoints.get(pointIndex).isScored()) {
						long examStartTime = Utils.Str2Long(Utils.getSharedPrefrences(context, "startTime"),
								"yyyy-MM-dd  HH:mm:ss");

//						allPoints.get(pointIndex).setScoreTime(System.currentTimeMillis() - examStartTime);
						String str = "特殊考点:" + (pointIndex + 1) + allPoints.get(pointIndex).getContent() + "第一次打分："
								+ realScore + "用时:"
								+ (System.currentTimeMillis() - examStartTime) / (1000 * 60 * 60 * 24) + "s";
						SaveNetRequestLog2Local.SaveHandleInfo2Local(str);
					}

					allPoints.get(pointIndex).setScore(realScore + "");

					allPoints.get(pointIndex).setScored(true);

					mGetScoreInterface.afterClick(allPoints);
				}

				@Override
				public void beforeTextChanged(CharSequence s, int start, int count, int after) {

				}

				@Override
				public void afterTextChanged(Editable s) {

				}
			});
			if (allPoints.get(pointIndex).isScored()) {
				holderSpec.item_subtractPoint_spec.setHint("剩余分数：" + allPoints.get(pointIndex).getScore());
			}
		}

		return convertView;
	}

	class ViewHolderNor implements CustomSeekBar.OnSeekBarChangeListener {

		public TextView item_number;
		public TextView item_content;
		private TextView item_ponit;
		public CustomSeekBar pointBar;
		private int mPosition;

		public ViewHolderNor(int position) {
			mPosition = position;
		}

		@Override
		public void OnProgressChanged(int id, int min, int max, int space, int progress) {

		}

		@Override
		public void OnProgressUpdated(int id, int min, int max, int space, int progress) {
			int potint = 0;

			if (progress % space > space / 2) {
				potint = progress / space + 1;
				int progress_ex = (potint) * space + min;
				pointBar.setProgress(progress_ex);
			} else {
				potint = progress / space;
				int progress_ex = (potint) * space + min;
				pointBar.setProgress(progress_ex);
			}

			// 改变刻度颜色
			pointBar.reSetMarkTextColors();

			pointBar.setMarkTextColor(potint, context.getResources().getColor(R.color.red_btn_bg_color));

			// 记录正常考点第一次打分的时间
			if (!allPoints.get(pointIndex).getTest_term().get(mPosition).isScored()) {

				long examStartTime = Utils.Str2Long(Utils.getSharedPrefrences(context, "startTime"),
						"yyyy-MM-dd HH:mm:ss");


				String str = "";

				str = "考点：" + (pointIndex + 1) + "考核项：" + (mPosition + 1) + "第一次打分：" + potint + "用时:"
						+ (System.currentTimeMillis() - examStartTime) /1000 + "s";

				SaveNetRequestLog2Local.SaveHandleInfo2Local(str);
			}
			// 改变打分的状态值
			allPoints.get(pointIndex).getTest_term().get(mPosition).setReal(potint + "");
			allPoints.get(pointIndex).getTest_term().get(mPosition).setScored(true);

			// changTextColar();
			mGetScoreInterface.afterClick(allPoints);
		}

		@Override
		public String OnPopupTextChanged(int id, int min, int max, int space, int progress) {
			// TODO Auto-generated method stub
			String str = "";
			if (progress % space > space / 2) {
				str = String.valueOf(((progress / space + 1) * space + min) / space);
			} else {
				str = String.valueOf(((progress / space) * space + min) / space);
			}
			return str + "分";
		}
	}

	class ViewHolderSpec {
		public TextView item_number_spec;// 特殊考点索引
		public TextView item_content_spec;// 特殊考点详情
		public EditText item_subtractPoint_spec;// 特殊考点扣分数
	}

	public void setGetScoreInterface(GetScoreInterface mGetScoreInterface) {
		this.mGetScoreInterface = mGetScoreInterface;
	}

	public interface GetScoreInterface {
		public void afterClick(ArrayList<GradePointBean_Local> data);
	}

	public ArrayList<GradePointBean_Local> getAllPointData() {
		return allPoints;
	}
}
*/