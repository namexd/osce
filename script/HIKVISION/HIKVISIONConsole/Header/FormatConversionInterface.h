/** @file       FormatConversionDefine.h
 *  @note       HangZhou Hikvision Digital Technology Co., Ltd. All Right Reserved.
 *  @brief      Definitions of interfaces used by APIs of Media Format Conversion dynamic library
 *
 *  @author     Media Play SDK Team of Hikvision
 *
 *  @version    V4.1.0
 *  @date       2014/03/03
 *
 *  @warning
 */
#ifndef _FC_INTERFACE_H_
#define _FC_INTERFACE_H_

#include "../Header/FormatConversionDefine.h"

#ifdef __cplusplus
extern "C" {
#endif 

	/** @fn     FC_GetSDKVersion(void)
	 *  @brief  获取格式转换库版本号
	 *  @param
	 *  @return |基线|编译日期 年(0~31)|月(1~12)|日(1~31) |主    |次    |修正  |  测试|
				  2bits       5bits        4bits     5bits   4bits  4bits  4bits  4bits
	 *  @note   [可选接口]
	 */
	FC_API unsigned int __stdcall FC_GetSDKVersion(void);


	/** @fn     FC_CreateHandle()
	 *  @brief  创建转换句柄
	 *  @param
	 *  @return 成功返回转换句柄，失败返回NULL；
	 *  @note   [必须调用]
	 */
	FC_API FCHANDLE __stdcall FC_CreateHandle(void);


	/** @fn     FC_SetSourceSessionInfo(const FCHANDLE hFC, const int nProtocolType, const FC_SESSION_INFO* pstSessionInfo);
	 *  @brief  设置描述源数据流的交互信息
	 *  @param  hFC             [IN]    - 转换句柄
	 *          nProtocolType   [IN]    - 网络协议类型。FC_PROTOCOL_NULL / FC_PROTOCOL_HIK / FC_PROTOCOL_RTSP
	 *          pstSessionInfo  [IN]    - 描述源数据的交互信息
	 *  @return 成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [流模式必须调用] 该接口适用于通过网络协议获取到的流数据。如果源数据是文件，则无需调用。
	 *  @warn   v4.1.0版本，暂不支持FC_PROTOCOL_RTSP和对应的SDP输入。
	 */
	FC_API int  __stdcall FC_SetSourceSessionInfo(const FCHANDLE hFC, const int nProtocolType, const FC_SESSION_INFO* pstSessionInfo);


	/** @fn     FC_SetTargetMediaInfo(const FCHANDLE hFC, const FC_MEDIA_INFO* pstTargetInfo)
	 *  @brief  设置目标数据媒体信息
	 *  @param  hFC             [IN]    - 转换句柄；
	 *          pstTargetInfo   [IN]    - 目标数据媒体信息，不能为NULL，必须正确填写，否则转换无法开始；
	 *  @return 成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [必须调用]
	 */
	FC_API int  __stdcall FC_SetTargetMediaInfo(const FCHANDLE hFC, const FC_MEDIA_INFO* pstTargetInfo);


	/** @fn     FC_GetSourceMediaInfo(const FCHANDLE hFC, FC_MEDIA_INFO* pstSourceInfo);
	 *  @brief  获取源数据的媒体信息
	 *  @param  hFC             [IN]    - 转换句柄
	 *          pstSourceInfo   [OUT]   - 描述源数据的媒体信息
	 *  @return 成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [可选接口]，有些详细信息无法获取，具体视数据格式
	 */
	FC_API int  __stdcall FC_GetSourceMediaInfo(const FCHANDLE hFC, FC_MEDIA_INFO* pstSourceInfo);


	/** @fn     FC_GetTargetSessionInfo(const FCHANDLE hFC, const int nProtocolType, FC_SESSION_INFO* pstSessionInfo);
	 *  @brief  获取描述目标数据流的交互信息
	 *  @param  hFC             [IN]        - 转换句柄
	 *          nProtocolType   [IN]        - 网络协议类型
	 *          pstSessionInfo  [IN][OUT]   - 描述目标数据的交互信息
	 *  @return 成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [可选接口] 流模式时调用，可获取交互信息。
	 */
	FC_API int  __stdcall FC_GetTargetSessionInfo(const FCHANDLE hFC, const int nProtocolType, FC_SESSION_INFO* pstSessionInfo);


	/** @fn     FC_Start(const FCHANDLE hFC, const char* szSrcPath, const char* szTgtPath)
	 *  @brief  开始转换
	 *  @param  hFC             [IN]    - 转换句柄；
	 *          szSrcPath       [IN]    - 源文件路径；
	 *          szTgtPath       [IN]    - 目标文件路径；
	 *  @return 成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [必须调用]
	 */
	FC_API int  __stdcall FC_Start(const FCHANDLE hFC, const char* szSrcPath, const char* szTgtPath);


	/** @fn     FC_InputSourceData(const FCHANDLE hFC, FC_DataType enType, const unsigned char* pData, const unsigned int nDataLen)
	 *  @brief  输入待转换的流数据
	 *  @param  hFC             [IN]    - 转换句柄；
	 *          enType          [IN]    - 流数据类型；
	 *          pData           [IN]    - 流数据指针；
	 *          nDataLen        [IN]    - 流数据长度
	 *  @return	成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [选择调用] 该接口只适用于源数据为数据流的情况。文件模式无效
	 */
	FC_API int  __stdcall FC_InputSourceData(const FCHANDLE hFC, FC_DataType enType, const unsigned char* pData, const unsigned int nDataLen);


	/** @fn     FC_RegisterTargetDataCallback(const FCHANDLE    hFC,
	 *                                        void(__stdcall* TargetDataCB)(unsigned int   nTrackIndex,
	 *                                                                      unsigned int   nDataType,
	 *                                                                      unsigned char* pData,
	 *                                                                      unsigned int   nDataLen,
	 *                                                                      void*          pUser)
	 *                                        void*             pUser);
	 *  @brief  注册转换后目标数据的回调函数
	 *  @param  hFC             [IN]    - 转换句柄；
	 *          pUser           [IN]    - 用户数据指针；
	 *  @return 成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [选择调用]该接口只适用于目标数据为数据流的情况
	 */
	FC_API int  __stdcall FC_RegisterTargetDataCallback(const FCHANDLE	hFC,
		void(__stdcall* TargetDataCB)(unsigned int   nTrackIndex,
			unsigned int   nDataType,
			unsigned char* pData,
			unsigned int   nDataLen,
			void*          pUser),
		void*          pUser);


	/** @fn     FC_Pause(const FCHANDLE hFC);
	 *  @brief  暂停转码
	 *  @param  hFC             [IN]    - 转换句柄；
	 *          nPausse         [IN]    - 1:暂停，0:恢复，其他值非法
	 *
	 *  @return 成功，返回FC_OK；失败，返回错误码
	 *
	 *  @note   [选择调用] 仅在源为文件时可调用
	 */
	FC_API int  __stdcall FC_Pause(const FCHANDLE hFC, unsigned int nPause);


	/** @fn     FC_Stop(const FCHANDLE hFC)
	 *  @brief  停止转换
	 *  @param  hFC             [IN]    - 转换句柄；
	 *  @return 成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [必须调用]
	 */
	FC_API int  __stdcall FC_Stop(const FCHANDLE hFC);


	/** @fn     FC_GetProgress(const FCHANDLE hFC，float* pfPercent)
	 *  @brief  获取转换进度
	 *  @param  hFC             [IN]        - 转换句柄；
	 *          pfPercent       [IN][OUT]   - 转换进度百分比指针
	 *  @return	成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [选择调用] 只有在源数据为文件时有效
	 */
	FC_API int	__stdcall	FC_GetProgress(const FCHANDLE hFC, float* pfPercent);


	/** @fn     FC_DestroyHandle(const FCHANDLE hFC)
	 *  @brief  销毁转换句柄
	 *  @param  hFC             [IN]        - 转换句柄；
	 *  @return 成功返回FC_OK，失败返回错误码
	 *
	 *  @note   [必须调用]
	 */
	FC_API int  __stdcall FC_DestroyHandle(const FCHANDLE hFC);

#ifdef __cplusplus
}
#endif 

#endif //_FC_INTERFACE_H_
