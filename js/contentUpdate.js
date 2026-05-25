const patientList = document.querySelector('.patient-dashboard');

function renderDoctorDashboard() {

    
    patientList.innerHTML = `
        <table>
                            <thead>
                                <tr>
                                    <th>{{patient_id}}</th>
                                    <th>{{patient_name}}</th>
                                    <th>{{doc_name}}</th>
                                    <th>{{apt_date}}</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{patient_id}}</td>
                                    <td>{{patient_name}}</td>
                                    <td>{{doc_name}}</td>
                                    <td>{{apt_date}}</td>
                                    <td>
                                        <button class="btn btn-primary">Approve</button>
                                        <button class="btn btn-secondary">Decline</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{patient_id}}</td>
                                    <td>{{patient_name}}</td>
                                    <td>{{doc_name}}</td>
                                    <td>{{apt_date}}</td>
                                    <td>
                                        <button class="btn btn-primary">Approve</button>
                                        <button class="btn btn-secondary">Decline</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
    `;
}