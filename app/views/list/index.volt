<style>
    table, td, th {
        border: 1px solid #ddd;
        text-align: left;
    }

    table {
        border-collapse: collapse;
    }

    th, td {
        padding: 15px;
    }
</style>
{% for item in result %}
{% if loop.first %}
<pre>
<table>
    <tr>
        <th>#</th>
        <th>From</th>
        <th>Message</th>
    </tr>
    {% endif %}
    <tr>
        <td>{{ loop.index }}</td>
        <td>{{ item['from'] }}</td>
        <td>{{ item['message'] }}</td>
    </tr>
    {% if loop.last %}
</table>
</pre>
{% endif %}
{% endfor %}
</pre>