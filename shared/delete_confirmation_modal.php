<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content" id="deleteForm">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Підтвердження видалення</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Закрити"></button>
            </div>
            <div class="modal-body">
                Ви дійсно хочете видалити користувача <strong id="deleteUserName"></strong>?
                <input type="hidden" name="id" id="deleteUserId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="submit" class="btn btn-danger">Видалити</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const modal = document.getElementById('deleteConfirmationModal');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            const userName = button.getAttribute('data-name');
            const action = button.getAttribute('data-action');

            modal.querySelector('#deleteUserId').value = userId;
            modal.querySelector('#deleteUserName').textContent = userName;
            modal.querySelector('#deleteForm').setAttribute('action', action);
        });
    });
</script>
