��    1      �  C   ,      8  `   9     �  2   �     �     �  Q  �     @  i  L     �     �     �     �     �       	        "     5     F  
   W     b  e   f     �     �  	   �  9   �  3   .	  t   b	  z   �	     R
     d
     i
     }
     �
     �
     �
     �
     �
     �
  L   �
  Q   #  >   u     �     �  	   �  H   �  w     S   �  U   �  =  A  t        �  3        D     P  V  ]     �  ^  �          6     K     X     `     n     �     �     �     �     �     �  h        m  
   |     �  ?   �  =   �  n     �   z          !     %     6     >     M  	   [     e     }     �  8   �  m   �  H   M     �     �     �  R   �  �     X   �  T            #                                  
              -      *   &               0             	   +      $   %      ,                   1           .      (             )             /       '                         !         "        'i' is used when the caller pushes an invalid button, and 't' is used when there is no response. Add IVR After three invalid attempts, the line is hung up. Announcement Change Name Check this box to have this option return to a parent IVR if it was called from a parent IVR. If not, it will go to the chosen destination.<br><br>The return path will be to any IVR that was in the call path prior to this IVR which could lead to strange results if there was an IVR called in the call path but not immediately before this Conferences Custom App<span><br>ADVANCED USERS ONLY<br><br>Uses Goto() to send caller to a custom context.<br><br>The context name should start with "custom-", and be in the format custom-context,extension,priority. Example entry:<br><br><b>custom-myapp,s,1</b><br><br>The <b>[custom-myapp]</b> context would need to be created and included in extensions_custom.conf</span> Custom Applications Day Night Mode Decrease Options Delete Digital Receptionist Directory Context Edit Menu Enable Direct Dial Enable Directory Extended Routing Extensions IVR If those options aren't supplied, the default 't' is to replay the menu three times and then hang up, Increase Options Instructions Languages Let callers into the IVR dial '#' to access the directory Let callers into the IVR dial an extension directly Message to be played to the caller. To add additional recordings please use the "System Recordings" MENU to the left Message to be played to the caller.<br><br>You must install and enable the "Systems Recordings" Module to edit this option Misc Destinations None Phonebook Directory Queues Return to IVR Ring Groups Save Speed dial functions Submit Changes Terminate Call The amount of time (in seconds) before the 't' option, if specified, is used There is a problem with install.sql, cannot re-create databases. Contact support
 This changes the short name, visible on the right, of this IVR Time Conditions Timeout Voicemail When # is selected, this is the voicemail directory context that is used When creating a menu option, apart from the standard options of 0-9,* and #, you can also use 'i' and 't' destinations. You use the Digital Receptionist to make IVR's, Interactive Voice Responce systems. and the default 'i' is to say 'Invalid option, please try again' and replay the menu. Project-Id-Version: 1.3
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2007-04-19 21:45+0100
PO-Revision-Date: 2008-01-31 16:38+0100
Last-Translator: Alexander Kozyrev <ceo@postmet.com>
Language-Team: Russian <faq@postmet.com>
MIME-Version: 1.0
Content-Type: text/plain; charset=iso-8859-5
Content-Transfer-Encoding: 8bit
 'i' ������������, ���� ����������� ����� �������� ������, � 't' ������������ ��� ��������� ��� �������� �� ��������. �������� ������������� ���� ����� ���� �������� ������� ���������� �����������. ����������� �������� ��� �������� �����, ���� ����� ���������� � ������������ ����, ���� ����� ���� ��������� �� ������� ����. ���� �� ��������, ����� ��������� �� ����� ����������.<br><br>������� ����� �������������� � � ����� ������ ����, �� ������� ����� ������������ ����� � ����������� ����, �� ������� � ��������������� ����� �������� � ����������� �����������. ����������� ������ ����������<span><br>¾�̺� ���½˼ ���̷���µ�ϼ<br>����������� ������� Goto() ��� ��������������� � custom ��������.<br><br>�������� ��������� ������ ���������� � "custom-" � ���� � ������� custom-context,extension,���������. ��������:<br><b>custom-app,s,1</b><br>�������� <b>[custom-app]</b> ������ ���� ������ � extensions_custom.conf</span ����������� ���������� �������/������ ����� ������ ����� ������� ������������� �������� ���������� �������� ���� ��������� ������ ������ ��������� ������ � ���������� ����������� ������������� ���������� ����� ������������� ���� ���� ��� ����� �� �������������, �� ��������� 't' ������������� ��������� ���� ��� ���� � ������ ������, �������� ����� ���������� ����� �������� ����������� ������ '#' � ���� ����� ����� � ���������� ��������� ������������ ������� �������� �� ���������� ������. ���������, ������� ������� �����������. ����� ������� ������ ����������� ������ ������ ��������� � ���� �����. ���������, ������� ������� �����������.<br><br>����� �������������� � ������������ ������ ������ ��������� ����� �������� ��� ���� � ���� �����. ��������� ����������� ��� ���������� ����� ������� ������� � ���� ������ ������ ��������� ������� �������� ������ ��������� ��������� ������������� ������ ����� �������� (� ��������), ���� ������������ ����� 't' �������� �������� �� �������� install.sql, ���������� ����������� ���� ������. ���������� � ������ ���������
 ����� �������� �������� ��� ���������� ����, ������� ������������ ������ ������� �� ������� ������� ��������� ����� ���� ������� '#' ����� �������������� ���� �������� ��� ���������� ��������� ����� ��� �������� ���� ����������� ����������� ������� 0-9, *, � #. ���� ����������� ����� ������������ 'i' (invalid number) � 't'(timeout) � �������� ����������. �� ����������� ������ ������������� ��� �������� ������� �������������� ���������� ����. ��������� 'i' �������� '�������� �����, �������� ��� ���' � ����� ���������� � ����. 