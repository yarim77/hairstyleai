$(document).ready(function () {
    let tagPopup = null;
    let searchTimeout = null;
    let lastSelection = null;

    $('#editor').on('input', function () {
        const sel = window.getSelection();
        if (sel.rangeCount === 0) return;

        const range = sel.getRangeAt(0);
        const container = range.startContainer;
        const offset = range.startOffset;

        let charBeforeCursor = '';

        if (container.nodeType === Node.TEXT_NODE) {
            charBeforeCursor = container.nodeValue[offset - 1];
        } else if (container.nodeType === Node.ELEMENT_NODE && offset > 0) {
            const nodeBeforeCursor = container.childNodes[offset - 1];
            if (nodeBeforeCursor.nodeType === Node.TEXT_NODE) {
                charBeforeCursor = nodeBeforeCursor.nodeValue.slice(-1);
            }
        }

        if (charBeforeCursor === '#') {
            saveSelection();
            showTagPopup();

            $('#rb-tag-list').empty().append('<div class="loadingOverlay loadingOverlay3"><div class="spinner3"></div></div>');
            updateTagList('');
        }
    });
    

    // 팝업 생성 및 위치 지정
    function showTagPopup() {
        if ($('#rb-tag-popup').length === 0) {
            tagPopup = $('<div id="rb-tag-popup"></div>').hide();

            let d_title = $('<h4 class="font-B">게시물 태그</h4>');
            let d_chk = $('<div class="rb_tag_chk"><input type="checkbox" id="rb-tag-my"><label for="rb-tag-my">내가 쓴 글</label></div>');
            let searchInput = $('<input type="text" id="rb-tag-search" placeholder="태그 또는 검색어 입력" autocomplete="off"/>');

            tagPopup.append(d_title, d_chk, searchInput).append('<ul id="rb-tag-list"></ul>');
            $('body').append(tagPopup);
        }

        let selection = window.getSelection();
        let range = selection.getRangeAt(0);
        let rect = getCaretPosition(range);

        const popupHeight = $('#rb-tag-popup').outerHeight();
        const popupWidth = $('#rb-tag-popup').outerWidth();
        const windowWidth = $(window).width();
        const windowHeight = $(window).height();

        let popupTop = rect.bottom + window.scrollY + 10;
        let popupLeft = rect.left + window.scrollX;

        if (popupTop + popupHeight > windowHeight) {
            popupTop = windowHeight - popupHeight - 10;
        }
        if (popupLeft + popupWidth > windowWidth) {
            popupLeft = windowWidth - popupWidth - 10;
        }
        if (popupLeft < 0) {
            popupLeft = 10;
        }
        if (popupTop < 0) {
            popupTop = 10;
        }

        tagPopup.css({ top: popupTop + 'px', left: popupLeft + 'px' }).fadeIn(100);
        updateTagList('');
    }

    // 커서 위치 가져오기
    function getCaretPosition(range) {
        let rect = range.getBoundingClientRect();
        if (rect.width === 0 && rect.height === 0) {
            let span = document.createElement('span');
            range.insertNode(span);
            rect = span.getBoundingClientRect();
            span.remove();
        }
        return rect;
    }

    // 게시물 목록 업데이트
    function updateTagList(query) {
        clearTimeout(searchTimeout);

        let list = $('#rb-tag-list');

        // ✅ 로딩 인디케이터가 없으면 추가
        if ($('#rb-tag-list .loadingOverlay3').length === 0) {
            list.append('<div class="loadingOverlay loadingOverlay3"><div class="spinner3"></div></div>');
        }

        // ✅ `#` 입력 즉시 로딩 UI 보이기
        $('.loadingOverlay3').show();

        searchTimeout = setTimeout(() => {
            let isMyPostChecked = $('#rb-tag-my').prop('checked');

            $.ajax({
                url: g5Config.g5_editor_url + '/plugin/tag/ajax.result.php',
                type: 'POST',
                data: { search: query, mypost: isMyPostChecked ? '1' : '0' },
                dataType: 'json',
                success: function (data) {
                    list.empty(); // ✅ 기존 리스트 초기화

                    if (!query && data.length > 0) {
                        list.append('<li class="rb-tag-item info">엔터 입력시 검색어를 링크로 추가 합니다.</li>');
                    }

                    if (data.length === 0) {
                        list.append('<li class="rb-tag-item no-result">검색 결과 없음</li>');
                    } else {
                        data.forEach(item => {
                            let listItem = $(`
                                <li class="rb-tag-item" data-url="${item.url}">
                                    <dd class='rb_tag_tit font-B cut'>${item.title}</dd>
                                    <dd class='rb_tag_date'>${item.wr_name}　${item.wr_datetime}</dd>
                                    <dd class='rb_tag_bbs'>${item.bo_subject}</dd>
                                    <img src='${item.thumbnail}' alt="${item.title}">
                                </li>
                            `);
                            list.append(listItem);
                        });
                    }
                },
                error: function () {
                    list.empty().append('<li class="rb-tag-item error">검색 중 오류 발생</li>');
                },
                complete: function () {
                    // ✅ AJAX 완료 후 로딩 UI 숨김
                    $('.loadingOverlay3').hide();
                }
            });
        }, 300);
    }


    // 체크박스 변경 시 업데이트
    $(document).on('change', '#rb-tag-my', function () {
        let searchQuery = $('#rb-tag-search').val().trim();
        updateTagList(searchQuery);
    });

    // 검색 입력 시 업데이트
    $(document).on('input', '#rb-tag-search', function () {
        let searchQuery = $(this).val().trim();
        updateTagList(searchQuery);
    });

    // `rb-tag-item` 클릭 시 에디터에 삽입
    $(document).on('click', '#rb-tag-list .rb-tag-item', function () {
        let title = $(this).find('.rb_tag_tit').text();
        let url = $(this).attr('data-url');

        if (!title || !url) {
            console.warn('제목 또는 URL이 없습니다.');
            return;
        }

        insertTag(title, url);
    });

    // `Enter` 입력 시 태그 삽입
    $(document).on('keydown', function (e) {
        if (e.key === 'Enter' && $('#rb-tag-popup').is(':visible')) {
            e.preventDefault();

            let query = $('#rb-tag-search').val().trim();

            if (query) {
                let searchUrl = g5Config.g5_bbs_url + `/board.php?bo_table=` + g5Config.g5_bo_table + `&sop=and&sfl=wr_subject%7C%7Cwr_content&stx=${encodeURIComponent(query)}`;
                insertTag(query, searchUrl);
            } else {
                alert('검색어를 입력하세요.');
            }
        }
    });

    // 태그 삽입 (태그 삽입 시 `#` 제거)
    function insertTag(title, url) {
        restoreSelection();
        deleteHashBeforeCursor(); // 🌟 `#`을 커서 위치에서 삭제

        let tagHtml = `<div class="rb_tag" contenteditable="false"><a href="${url}" target="_blank" title="게시물 바로가기"># ${title}</a></div>&nbsp;`;
        insertHTMLAtCursor(tagHtml);

        $('#rb-tag-popup').fadeOut(100);
    }

    // 🌟 커서 앞의 `#`을 삭제하는 함수 추가
    function deleteHashBeforeCursor() {
        let sel = window.getSelection();
        if (sel.rangeCount) {
            let range = sel.getRangeAt(0);
            let container = range.startContainer;
            let offset = range.startOffset;

            if (container.nodeType === Node.TEXT_NODE && offset > 0) {
                let text = container.nodeValue;
                if (text[offset - 1] === "#") {
                    container.nodeValue = text.slice(0, offset - 1) + text.slice(offset);
                    range.setStart(container, offset - 1);
                    range.setEnd(container, offset - 1);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
        }
    }

    // `.rb_tag` 안으로 커서가 들어가지 않게 설정
    $(document).on('mousedown keydown', '.rb_tag', function (e) {
        e.preventDefault();
    });

    // ESC 키 입력 시 닫기
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#rb-tag-popup').is(':visible')) {
            $('#rb-tag-popup').fadeOut(100);
        }
    });

    // 에디터 외부 클릭 시 닫기
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#rb-tag-popup').length) {
            $('#rb-tag-popup').fadeOut(100);
        }
    });
});
