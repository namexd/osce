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
	 *  @brief  ��ȡ��ʽת����汾��
	 *  @param
	 *  @return |����|�������� ��(0~31)|��(1~12)|��(1~31) |��    |��    |����  |  ����|
				  2bits       5bits        4bits     5bits   4bits  4bits  4bits  4bits
	 *  @note   [��ѡ�ӿ�]
	 */
	FC_API unsigned int __stdcall FC_GetSDKVersion(void);


	/** @fn     FC_CreateHandle()
	 *  @brief  ����ת�����
	 *  @param
	 *  @return �ɹ�����ת�������ʧ�ܷ���NULL��
	 *  @note   [�������]
	 */
	FC_API FCHANDLE __stdcall FC_CreateHandle(void);


	/** @fn     FC_SetSourceSessionInfo(const FCHANDLE hFC, const int nProtocolType, const FC_SESSION_INFO* pstSessionInfo);
	 *  @brief  ��������Դ�������Ľ�����Ϣ
	 *  @param  hFC             [IN]    - ת�����
	 *          nProtocolType   [IN]    - ����Э�����͡�FC_PROTOCOL_NULL / FC_PROTOCOL_HIK / FC_PROTOCOL_RTSP
	 *          pstSessionInfo  [IN]    - ����Դ���ݵĽ�����Ϣ
	 *  @return �ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [��ģʽ�������] �ýӿ�������ͨ������Э���ȡ���������ݡ����Դ�������ļ�����������á�
	 *  @warn   v4.1.0�汾���ݲ�֧��FC_PROTOCOL_RTSP�Ͷ�Ӧ��SDP���롣
	 */
	FC_API int  __stdcall FC_SetSourceSessionInfo(const FCHANDLE hFC, const int nProtocolType, const FC_SESSION_INFO* pstSessionInfo);


	/** @fn     FC_SetTargetMediaInfo(const FCHANDLE hFC, const FC_MEDIA_INFO* pstTargetInfo)
	 *  @brief  ����Ŀ������ý����Ϣ
	 *  @param  hFC             [IN]    - ת�������
	 *          pstTargetInfo   [IN]    - Ŀ������ý����Ϣ������ΪNULL��������ȷ��д������ת���޷���ʼ��
	 *  @return �ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [�������]
	 */
	FC_API int  __stdcall FC_SetTargetMediaInfo(const FCHANDLE hFC, const FC_MEDIA_INFO* pstTargetInfo);


	/** @fn     FC_GetSourceMediaInfo(const FCHANDLE hFC, FC_MEDIA_INFO* pstSourceInfo);
	 *  @brief  ��ȡԴ���ݵ�ý����Ϣ
	 *  @param  hFC             [IN]    - ת�����
	 *          pstSourceInfo   [OUT]   - ����Դ���ݵ�ý����Ϣ
	 *  @return �ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [��ѡ�ӿ�]����Щ��ϸ��Ϣ�޷���ȡ�����������ݸ�ʽ
	 */
	FC_API int  __stdcall FC_GetSourceMediaInfo(const FCHANDLE hFC, FC_MEDIA_INFO* pstSourceInfo);


	/** @fn     FC_GetTargetSessionInfo(const FCHANDLE hFC, const int nProtocolType, FC_SESSION_INFO* pstSessionInfo);
	 *  @brief  ��ȡ����Ŀ���������Ľ�����Ϣ
	 *  @param  hFC             [IN]        - ת�����
	 *          nProtocolType   [IN]        - ����Э������
	 *          pstSessionInfo  [IN][OUT]   - ����Ŀ�����ݵĽ�����Ϣ
	 *  @return �ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [��ѡ�ӿ�] ��ģʽʱ���ã��ɻ�ȡ������Ϣ��
	 */
	FC_API int  __stdcall FC_GetTargetSessionInfo(const FCHANDLE hFC, const int nProtocolType, FC_SESSION_INFO* pstSessionInfo);


	/** @fn     FC_Start(const FCHANDLE hFC, const char* szSrcPath, const char* szTgtPath)
	 *  @brief  ��ʼת��
	 *  @param  hFC             [IN]    - ת�������
	 *          szSrcPath       [IN]    - Դ�ļ�·����
	 *          szTgtPath       [IN]    - Ŀ���ļ�·����
	 *  @return �ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [�������]
	 */
	FC_API int  __stdcall FC_Start(const FCHANDLE hFC, const char* szSrcPath, const char* szTgtPath);


	/** @fn     FC_InputSourceData(const FCHANDLE hFC, FC_DataType enType, const unsigned char* pData, const unsigned int nDataLen)
	 *  @brief  �����ת����������
	 *  @param  hFC             [IN]    - ת�������
	 *          enType          [IN]    - ���������ͣ�
	 *          pData           [IN]    - ������ָ�룻
	 *          nDataLen        [IN]    - �����ݳ���
	 *  @return	�ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [ѡ�����] �ýӿ�ֻ������Դ����Ϊ��������������ļ�ģʽ��Ч
	 */
	FC_API int  __stdcall FC_InputSourceData(const FCHANDLE hFC, FC_DataType enType, const unsigned char* pData, const unsigned int nDataLen);


	/** @fn     FC_RegisterTargetDataCallback(const FCHANDLE    hFC,
	 *                                        void(__stdcall* TargetDataCB)(unsigned int   nTrackIndex,
	 *                                                                      unsigned int   nDataType,
	 *                                                                      unsigned char* pData,
	 *                                                                      unsigned int   nDataLen,
	 *                                                                      void*          pUser)
	 *                                        void*             pUser);
	 *  @brief  ע��ת����Ŀ�����ݵĻص�����
	 *  @param  hFC             [IN]    - ת�������
	 *          pUser           [IN]    - �û�����ָ�룻
	 *  @return �ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [ѡ�����]�ýӿ�ֻ������Ŀ������Ϊ�����������
	 */
	FC_API int  __stdcall FC_RegisterTargetDataCallback(const FCHANDLE	hFC,
		void(__stdcall* TargetDataCB)(unsigned int   nTrackIndex,
			unsigned int   nDataType,
			unsigned char* pData,
			unsigned int   nDataLen,
			void*          pUser),
		void*          pUser);


	/** @fn     FC_Pause(const FCHANDLE hFC);
	 *  @brief  ��ͣת��
	 *  @param  hFC             [IN]    - ת�������
	 *          nPausse         [IN]    - 1:��ͣ��0:�ָ�������ֵ�Ƿ�
	 *
	 *  @return �ɹ�������FC_OK��ʧ�ܣ����ش�����
	 *
	 *  @note   [ѡ�����] ����ԴΪ�ļ�ʱ�ɵ���
	 */
	FC_API int  __stdcall FC_Pause(const FCHANDLE hFC, unsigned int nPause);


	/** @fn     FC_Stop(const FCHANDLE hFC)
	 *  @brief  ֹͣת��
	 *  @param  hFC             [IN]    - ת�������
	 *  @return �ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [�������]
	 */
	FC_API int  __stdcall FC_Stop(const FCHANDLE hFC);


	/** @fn     FC_GetProgress(const FCHANDLE hFC��float* pfPercent)
	 *  @brief  ��ȡת������
	 *  @param  hFC             [IN]        - ת�������
	 *          pfPercent       [IN][OUT]   - ת�����Ȱٷֱ�ָ��
	 *  @return	�ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [ѡ�����] ֻ����Դ����Ϊ�ļ�ʱ��Ч
	 */
	FC_API int	__stdcall	FC_GetProgress(const FCHANDLE hFC, float* pfPercent);


	/** @fn     FC_DestroyHandle(const FCHANDLE hFC)
	 *  @brief  ����ת�����
	 *  @param  hFC             [IN]        - ת�������
	 *  @return �ɹ�����FC_OK��ʧ�ܷ��ش�����
	 *
	 *  @note   [�������]
	 */
	FC_API int  __stdcall FC_DestroyHandle(const FCHANDLE hFC);

#ifdef __cplusplus
}
#endif 

#endif //_FC_INTERFACE_H_
