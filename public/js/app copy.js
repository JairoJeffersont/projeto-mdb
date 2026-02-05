// =======================
// METHODS JQUERY
// =======================

// Method to auto-hide alerts
// This method automatically hides an alert element after a specified timeout (in seconds) 
// defined in the element's `data-timeout` attribute. After fading out, the element is removed from the DOM.
$.fn.autoHideAlert = function () {
    return this.each(function () {
        const $alert = $(this);
        const timeout = parseInt($alert.attr('data-timeout'));

        if (timeout > 0) {
            setTimeout(() => {
                $alert.fadeOut(500, function () {
                    $(this).remove();
                });
            }, timeout * 1000);
        }
    });
};


// Método para popular os partidos no select
$.fn.populatePartidos = function () {
    const $selectPartido = $(this);
    const selectedPartido = $selectPartido.attr('data-selected') || '';
    const legislatura = $selectPartido.attr('data-legislatura') || '';

    // Monta a URL dinamicamente
    let url = 'https://dadosabertos.camara.leg.br/api/v2/partidos?itens=100&ordem=ASC&ordenarPor=sigla';
    if (legislatura) {
        url += `&idLegislatura=${legislatura}`;
    }

    // Estado inicial
    $selectPartido.empty().append('<option>Carregando...</option>');

    // Requisição para buscar os partidos
    $.getJSON(url, function (data) {
        const partidos = data.dados || [];
        $selectPartido.empty().append('<option value="">Partido não informado</option>');

        partidos.forEach(partido => {
            const isSelected = partido.sigla === selectedPartido ? 'selected' : '';
            $selectPartido.append(`<option value="${partido.sigla}" ${isSelected}>${partido.sigla}</option>`);
        });
    });
};

// Método para popular os estados no select
$.fn.populateEstados = function () {
    const $selectEstado = $(this);
    const selectedUF = $selectEstado.attr('data-selected') || '';
    $selectEstado.empty().append('<option>Carregando...</option>');

    $.getJSON('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome', function (estados) {
        $selectEstado.empty().append('<option value="">Selecione o estado</option>');

        estados.forEach(estado => {
            const isSelected = estado.sigla === selectedUF ? 'selected' : '';
            $selectEstado.append(
                `<option value="${estado.sigla}" data-id="${estado.id}" ${isSelected}>${estado.nome}</option>`
            );
        });

        // Dispara change para popular municípios caso UF já esteja selecionada
        if (selectedUF) $selectEstado.trigger('change');
    });
};

// Método para popular os municípios baseado no estado selecionado
$.fn.populateMunicipios = function (estadoId) {
    const $selectMunicipio = $(this);
    const selectedMunicipio = $selectMunicipio.attr('data-selected') || '';
    $selectMunicipio.empty().append('<option>Carregando...</option>');

    if (!estadoId) {
        $selectMunicipio.empty().append('<option value="">Selecione o município</option>');
        return;
    }

    $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios`, function (municipios) {
        $selectMunicipio.empty().append('<option value="">Selecione o município</option>');

        municipios.forEach(municipio => {
            const isSelected = municipio.nome === selectedMunicipio ? 'selected' : '';
            $selectMunicipio.append(`<option value="${municipio.nome}" ${isSelected}>${municipio.nome}</option>`);
        });
    });
};

// Method to confirm action
// This method attaches a click event to elements to show a confirmation dialog.
// The message can be customized with the `data-message` attribute. If the user cancels,
// the default action is prevented.
$.fn.confirmAction = function () {
    return this.each(function () {
        $(this).on('click', function (e) {
            const message = $(this).attr('data-message') || 'Are you sure?';

            if (!confirm(message)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });
    });
};

// =======================
// AUXILIARY FUNCTIONS
// =======================

// Function to show the loading modal
// This function displays a modal with a message. It can optionally change the message
// after a delay (`delayMessage`) if the process takes longer than expected.
// The modal automatically closes after `autoCloseTime` milliseconds.
function showLoadingModal(initialMessage = 'Wait, processing...', delayMessage = 'This is taking longer than expected…', delayTime = 10000, autoCloseTime = 30000) {
    const $modal = $('#modalLoading');
    const $message = $modal.find('.modal-body p');

    $message.text(initialMessage);
    $modal.modal('show');

    // Change message if processing takes too long
    const messageTimeout = setTimeout(() => {
        $message.text(delayMessage);
    }, delayTime);

    // Auto-close the modal after a set time
    setTimeout(() => {
        clearTimeout(messageTimeout);
        $modal.modal('hide');
    }, autoCloseTime);
}

// Function to initialize events
// This function initializes all necessary event listeners for the page.
// It applies the confirmAction method and sets up clicks that trigger the loading modal.
function initEvents() {
    $('.confirm-action').confirmAction();

    // Clicks on elements that trigger the modal
    $(document).on('click', '.loading-modal', function () {
        const message = $(this).attr('data-modalMessage') || 'Wait, processing...';
        showLoadingModal(message);
    });

    // Mudança do estado para popular municípios (para qualquer formulário)
    $(document).on('change', '.estado', function () {
        const $form = $(this).closest('form');
        const estadoId = $(this).find(':selected').data('id');
        const $municipioSelect = $form.find('.municipio');
        $municipioSelect.populateMunicipios(estadoId);
    });
}

// =======================
// INITIALIZATION
// =======================

// On document ready:
// - Apply autoHideAlert to any alert elements with a data-timeout attribute
// - Initialize other events
$(document).ready(function () {
    $('.alert[data-timeout]').autoHideAlert();

    // Popula todos os selects de estado encontrados
    $('.estado').each(function () {
        $(this).populateEstados();
    });

    // Popula todos os selects de partido (caso existam)
    $('.partidos').each(function () {
        $(this).populatePartidos();
    });

    initEvents();
});
