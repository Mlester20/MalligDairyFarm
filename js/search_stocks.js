$(document).ready(function () {
    // Function to load live stock records
    function loadLiveStocks(query = '') {
        $.ajax({
            url: '../controllers/save_stocksController.php',
            method: 'GET',
            data: { action: 'fetch', query: query },
            dataType: 'json',
            success: function (data) {
                let tableContent = '';
                if (data.length > 0) {
                    data.forEach(function (stock) {
                        tableContent += `
                            <tr>
                                <td>${stock.id}</td>
                                <td>${stock.live_stock_name}</td>
                                <td>${stock.live_stock_code}</td>
                                <td>${stock.created_at}</td>
                            </tr>
                        `;
                    });
                } else {
                    tableContent = '<tr><td colspan="4" class="text-center">No records found</td></tr>';
                }
                $('#liveStockTable').html(tableContent);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching live stocks:', error);
            }
        });
    }

    // Load live stocks on page load
    loadLiveStocks();

    // Handle search input
    $('#searchStock').on('keyup', function () {
        const query = $(this).val();
        loadLiveStocks(query); // Pass the search query to the loadLiveStocks function
    });
});